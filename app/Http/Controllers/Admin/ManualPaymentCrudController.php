<?php

namespace App\Http\Controllers\Admin;

use ray;
use BigQuery;
use Carbon\Carbon;
use App\Models\Batch;
use App\Models\ManualPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ManualPaymentRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ManualPaymentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ManualPaymentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ManualPayment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/manual-payment');
        CRUD::setEntityNameStrings('Manual Payment', 'manual payments');

        $batchId = request('batch');
        if ($batchId == null) {
            CRUD::addClause('where', 'loaded_to_bigquery_datetime', null);
        } else {
            CRUD::addClause('where', 'batch_id', $batchId);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::column('drip_id');
        $batchId = request('batch');
        if ($batchId == null) {
            $this->crud->addButtonFromView('bottom', 'process', 'process', 'end');
        } else {
            CRUD::removeAllButtons();
            // CRUD::removeButton('create');
            // CRUD::removeButton('delete');
            // CRUD::removeButton('delete');
            CRUD::addButton('top', 'return to batches', 'model_function', 'returnToBatchView', 'beginning');
        }
        CRUD::column('event_datetime')->label('Payment date')->type('date');
        CRUD::column('customer_name')->label('Customer name');
        CRUD::column('email')->label('Customer email')->wrapper([
            'href' => function ($crud, $column, $entry, $related_key) {
                if ($entry->drip_id == 'unknown') {
                    return "mailto:{$entry->email}";
                } else {
                    return "https://www.getdrip.com/". config('manualpayments.drip_account_id') ."/subscribers/{$entry->drip_id}";
                    // return "https://www.getdrip.com/9317564/subscribers/";//{$entry->drip_id}";
                }
            },
            'target' => '_blank',
        ]);
        // CRUD::column('email');
        CRUD::column('product_id')->label('License Type')->value(
            function ($entry) {
                //dd($entry);
                return config('manualpayments.license_types')[$entry->product_id];
            }
        );

        //CRUD::column('order_id');
        // CRUD::column('product_id');
        // CRUD::column('product_name');
        CRUD::column('amount_collected')->type('number')->decimals(2)->prefix('$')->align('right');
        //CRUD::column('currency');
        //CRUD::column('payment_type');
        //CRUD::column('reviewed');
        //CRUD::column('loaded_to_bigquery_datetime');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }
    //////////////////////////////////////////////////////////////////////
    // PROCESS THE BATCH - Send to BigQuery
    //////////////////////////////////////////////////////////////////////
    protected function process()
    {
        $datasetId = 'cgc';
        $tableId = 'events';
        $sourceCount = 0;
        $sourceTotal = 0.00;
        // $destCount = 0;
        // $destTotal = 0.00;
        $successCount = 0;
        $failCount = 0;

        $payments = ManualPayment::where('loaded_to_bigquery_datetime', null)->get();
        if (count($payments) > 0) {
            $batchId = $this->getNextBatchId();

            ray($payments);
            foreach ($payments as $payment) {
                $sourceCount++;
                $sourceTotal += $payment->amount_collected;
                $record = [
                'sequence_id'       => (string)time(),
                'drip_id'           => $payment->drip_id,
                'email'             => $payment->email,
                'event'             => $payment->event,
                'event_datetime'    => $payment->event_datetime,
                'order_id'          => $payment->order_id,
                'product_id'        => $payment->product_id,
                'product_name'      => $payment->product_name,
                'amount_collected'  => $payment->amount_collected,
                'currency'          => $payment->currency,
                'payment_type'      => $payment->payment_type,
                'batch_id'          => $batchId,
                'source_id'         => $payment->id,
            ];
                // $recs[$pmtCount++] = ['data' => $record];
                // do the bigquery magic
                // $dataset = BigQuery::dataset($datasetId);
                // $table = $dataset->table($tableId);
                // $response = $table->insertRows([['data' => $record]]);
                $response = BigQuery::dataset($datasetId)->table($tableId)->insertRows([['data' => $record]]);
                if ($response->isSuccessful()) {
                    // ray($response->info());
                    $payment->loaded_to_bigquery_datetime = Carbon::now();
                    $payment->batch_id = $batchId;
                    $payment->save();
                    $successCount++;
                //return true;
                } else {
                    $failCount++;
                    ray($response->failedRows());
                    // foreach ($insertResponse->failedRows() as $row) {
                        //     foreach ($row['errors'] as $error) {
                        //         printf('%s: %s' . PHP_EOL, $error['reason'], $error['message']);
                        //     }
                        // }
                }
            }
            ray($successCount, $failCount);
            if ($failCount>0) {
                return false;
            } else {
                //ray(Auth::user());
                $this->reconcileBatch($batchId, $sourceCount, $sourceTotal);
                return true;
            }
        } else {
            return false;
        }
    }

    ////////////////////////////////////////////////////////////////////////
    public function getNextBatchId()
    {
        $result = DB::select(
            'select max(id) as max_id
            from batches'
        );
        if ($result) {
            if ($result[0]->max_id == null) {
                return 1;
            } else {
                return $result[0]->max_id + 1;
            }
        }
    }
    //////////////////////////////////////////////////////////////////////
    // RECONCILE THE BATCH - Query BigQuery
    //////////////////////////////////////////////////////////////////////
    protected function reconcileBatch(int $batchId, int $sourceCount, float $sourceTotal)
    {
        $datasetTable = config('manualpayments.bq_dataset_name') .'.'. config('manualpayments.bq_events_table_name');
        $sql =  "select count(0) as dest_count,
                sum(amount_collected) as dest_total
                from {$datasetTable} 
                where batch_id = {$batchId}";
        $jobConfig = BigQuery::query($sql)->useLegacySql(true);
        $rows = BigQuery::runQuery($jobConfig)->rows();
        $destCount = intval($rows->current()['dest_count']);
        $destTotal = floatval($rows->current()['dest_total']);
        ray($destCount, $destTotal);
        Batch::create([
            'batch_id'          => $batchId,
            'user_id'           => backpack_auth()->user()->id,
            'source_count'      => $sourceCount,
            'source_total'      => $sourceTotal,
            'destination_count' => $destCount,
            'destination_total' => $destTotal,

        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ManualPaymentRequest::class);

        CRUD::field('drip_id')->type('hidden')->value('');
        CRUD::field('payment_type')->type('hidden')->value('manual');
        // CRUD::field('product_id')->type('hidden')->value('');
        CRUD::field('order_id')->type('hidden')->value(uniqid());
        CRUD::field('currency')->type('hidden')->value('CAD');
        

        $this->crud->addField([
            'name' => 'event_datetime',
            'label' => 'Payment Date',
            'type' => 'date',
        ]);
        CRUD::field('customer_name')->type('text')->label('Customer Name');

        CRUD::field('email')->type('email')->label('Email Address');
        //CRUD::field('event')->label('License Type');

        $this->crud->addField([
            'name' => 'product_id',
            'label' => 'License Type',
            'type' => 'select_from_array',
            'options' => config('manualpayments.license_types'),
        ]);

        // $this->crud->addField([
        //     'name' => 'grams_per_day',
        //     'label' => 'Grams Per Day',
        //     'type' => 'select_from_array',

        // ]);
        //CRUD::field('event_datetime')->type('date')->label('Payment Date');
        // CRUD::field('order_id');
        // CRUD::field('product_id');
        // CRUD::field('product_name');
        CRUD::field('amount_collected')->type('number')->prefix('CAD $')->attributes(['step' => '.01']);
        // CRUD::field('currency');
        // CRUD::field('payment_type');
        // CRUD::field('reviewed');
        // CRUD::field('loaded_to_bigquery_datetime');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
