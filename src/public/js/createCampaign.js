// $(document).on('click', '#btn-save-new-campaign', function (e) {
//     e.preventDefault();

//     const $btn = $(this);
//     const $modal = $('#exampleModal'); // ID modal 
//     const title = $('#campaign-title').val();
//     const content = $('#campaign-content').val();

//     $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Save...');

//     $.ajax({
//         url: "{{ route('admin.campaigns.store') }}",
//         type: "POST",
//         data: {
//             _token: "{{ csrf_token() }}",
//             title: title,
//             content: content
//         },
//         success: function (response) {
//             if (response.success) {
//                 alert(response.message);

//                 const $select = $('#campaign_id');

//                 if ($select.length > 0) {

//                     const newOption = new Option(response.data.title, response.data.id, true, true);

//                     $select.append(newOption).trigger('change');

//                     if ($.fn.select2) {
//                         $select.trigger('change.select2');
//                     }
//                 }

//                 $('#exampleModal').modal('hide');
//                 $('#modalCreateCampaignForm')[0].reset();
//             }
//         },
//         error: function (xhr) {
//             const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
//             let errorText = "";
//             if (errors) {
//                 errorText = Object.values(errors).flat().join('\n');
//             } else {
//                 errorText = "Error System!";
//             }
//             alert(errorText);
//         },
//         complete: function () {
//             $btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i> Complete');
//         }
//     });
// });
