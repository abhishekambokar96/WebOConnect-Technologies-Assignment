/* ------------------------------------------------------------------------------
 *
 *  # Custom JS code
 *
 *  Place here all your custom js. Make sure it's loaded after app.js
 *
 * ---------------------------------------------------------------------------- */

$(document).ready(function(){

	// $('.select').select2({
    //     minimumResultsForSearch: Infinity
    // });

    if ($().uniform) 
    {
        $('.form-input-styled').uniform({
            fileButtonClass: 'action btn bg-pink-400'
        });

        $('.form-check-input-styled').uniform();
    }

    setTimeout(function(){
        $('.flash-class').slideUp('slow');
    }, 5000);

    if ($().DataTable) 
    {
        $.extend( true, $.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [{ 
                orderable: false,
                width: 100
            }],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            }
        });
    }

});

// function RegSuccMsg(data){
//     swal({
//         title: "Information",
//         text: data.message,
//         type: "success",
//         allowOutsideClick:false,
//         allowEscapeKey:false
//     }).then(function(){
//         location.reload(true);
//     })
// }

function RegSuccMsgNoReload(data){
    swal("Information",data.message,"success").then(function () {

    });
}

function RegErrMsgNoReload(data){
    swal("Information",data.message,"error").then(function () {

    });
}

function loginErrorMsg(data){
    swal("Information",data.message,"error");
}

function  redirect(data){
  window.location=data.link;
}   

function redirect_with_msg(data){
    swal({
        title: data.title,
        text: data.message,
        type: data.type,
        allowOutsideClick:false,
        allowEscapeKey:false
    }).then(function(){
        window.location=data.link;
    });
}

function RegErrorMsg(data){
     swal({
        text: data.message,
        type: "error"
    }).then(function(){
        location.reload(true);
    });
}
function RegSuccMsg(data){
    $('body').find('#anloader').remove();
    swal({
        text: data.message,
        type: "success"
    }).then(function(){
        location.reload(true);
    });
}
function RegSuccessMsg(data){
    swal("Success",data.message,"success");
}

function reload(){
    location.reload(true);
}
