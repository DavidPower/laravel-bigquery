@if ($crud->hasAccess('create'))

    <a href="javascript:void(0)" onclick="process(this)" data-route="{{ url($crud->route.'/process') }}" class="btn btn-primary" data-button-type="process">
<span class="ladda-label"><i class="la la-upload"></i> Process Batch</span>
</a>

@endif

@push('after_scripts')
<script>

    if (typeof process != 'function') {
      $("[data-button-type=import]").unbind('click');

      function process(button) {
          // ask for confirmation before deleting an item
        // new Noty({
        //     type: "warning",
        //     text: "please confirm",
        // }).show();
    
        //   e.preventDefault();
          var button = $(button);
          var route = button.attr('data-route');

          $.ajax({
              url: route,
              type: 'GET',
              success: function(result) {
                  // Show an alert with the result
                  console.log(result,route);
                  new Noty({
                      text: "Your batch has been processed!",
                      type: "success"
                  }).show();

                  // Hide the modal, if any
                  $('.modal').modal('hide');

                  crud.table.ajax.reload();
              },
              error: function(result) {
                  // Show an alert with the result
                  new Noty({
                      text: "We encountered a problem processing the batch. Please try again in a minute.",
                      type: "warning"
                  }).show();
              }
          });
      }
    }
</script>
@endpush