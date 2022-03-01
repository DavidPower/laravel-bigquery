<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BatchRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BatchCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BatchCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Batch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/batch');
        CRUD::setEntityNameStrings('batch', 'batches');
    }
    

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::removeAllButtons();
        CRUD::removeButton('create');
        CRUD::removeButton('show');
        CRUD::removeButton('update');
        CRUD::removeButton('delete');
        CRUD::column('batch_id')->label('Batch ID');
        CRUD::column('created_at')->label('Created date');
        CRUD::column('user_id')->label('Created by')->value(function ($entry) {
            return backpack_user($entry)->name;
        });
        CRUD::column('source_count')->label('Transaction count');
        CRUD::column('source_total')->type('number')->decimals(2)->prefix('$')->label('Batch value');
        CRUD::column('reconciled')->label('Reconciled')->type('text')
            ->value(
                function ($entry) {
                    if (($entry->source_count - $entry->destination_count == 0)
                        &&($entry->source_total - $entry->destination_total == 0)) {
                        return 'YES';
                    } else {
                        return 'NO';
                    }
                }
            )
            ->wrapper([
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if (($entry->source_count - $entry->destination_count == 0)
                        &&($entry->source_total - $entry->destination_total == 0)) {
                        // return '<i class="las la-check"></i>';
                        return 'badge badge-success';
                    //return true;
                    } else {
                        // return '<i class="las la-times"></i>';
                        // return false;
                        return 'badge badge-default';
                    }
                }
            ]);

        // CRUD::column('reconciled')->label('Reconciled')->type('check')->value(function ($entry) {
        //     // if (true) {
        //     if (($entry->source_count - $entry->destination_count == 0)
        //             &&($entry->source_total - $entry->destination_total == 0)) {
        //         // return '<i class="las la-check"></i>';
        //         return true;
        //     } else {
        //         // return '<i class="las la-times"></i>';
        //         return false;
        //     }
        // });
        CRUD::addButton('line', 'details', 'model_function', 'paymentsLink', 'beginning');
        // CRUD::column('destination_count');
        // CRUD::column('destination_total');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BatchRequest::class);

        CRUD::field('batch_id');
        CRUD::field('user_id');
        CRUD::field('source_count');
        CRUD::field('source_total');
        CRUD::field('destination_count');
        CRUD::field('destination_total');
        // CRUD::field('manualPayments');

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
