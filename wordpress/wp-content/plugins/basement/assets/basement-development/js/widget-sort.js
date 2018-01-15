jQuery(document).ready(function($) {
    if(!$('body').hasClass('widgets_access')){
        sllwSetupList($);
        $('.sllw-edit-item').addClass('toggled-off');
        sllwSetupHandlers($);
    }

    $(document).on('keyup', '.text-coper', function () {
        $(this).closest('.list-item ').find('.item-title').text($(this).val());
    });

    $(document).ajaxSuccess(function() {
        sllwSetupList($);
        $('.sllw-edit-item').addClass('toggled-off');
    });


    $(document).on('click','.widget_icon_select', function () {
       var $this = $(this),
           $spinner = $this.next('.spinner'),
           $icons = $spinner.next('.icon-chooser'),
           value = 'fa-' + $this.closest('.list-item').find('.widefat.text-coper').val();
        $spinner.addClass('is-active');
        $.ajax({
            type: 'POST',
            url: ajaxurl ? ajaxurl : '/wp-admin/admin-ajax.php',
            data: {
                'action': 'ajax-generate-icons',
                'param' : {
                    type: 'fontawesome',
                    id :  $this.data('id'),
                    key : $this.data('key')
                }
            },
            success: function(response, status){
                if(response) {
                    $icons.html(response);
                    $icons.find('input[value="'+value+'"]').prop('checked',true);
                }
                $spinner.removeClass('is-active').css({
                    'opacity' : '0',
                    'display' : 'none',
                    'visibility' : 'hidden'
                });
            }
        });
    });


    $(document).on('click','.icon-chooser input[type="radio"]', function () {
        $(this).closest('.list-item').find('.widefat.text-coper').val($(this).val().slice(3)).trigger('keyup');
    });

});

function sllwSetupList($){
    $( ".simple-link-list" ).sortable({
        items: '.list-item',
        opacity: 0.6,
        cursor: 'n-resize',
        axis: 'y',
        handle: '.moving-handle',
        placeholder: 'sortable-placeholder',
        start: function (event, ui) {
            ui.placeholder.height(ui.helper.height());
        },
        update: function() {
            updateOrder($(this));
        }
    });

    $( ".simple-link-list .moving-handle" ).disableSelection();
}


// All Event handlers
function sllwSetupHandlers($){
    $("body").on('click.sllw','.sllw-delete',function() {
        $(this).parent().parent().fadeOut(500,function(){
            var sllw = $(this).parents(".widget-content");
            $(this).remove();
            sllw.find('.order').val(sllw.find('.simple-link-list').sortable('toArray'));
            var num = sllw.find(".simple-link-list .list-item").length;
            var amount = sllw.find(".amount");
            amount.val(num);
        });
    });

    $("body").on('click.sllw','.sllw-add',function() {
        var sllw = $(this).parent().parent();
        var num = sllw.find('.simple-link-list .list-item').length + 1;

        sllw.find('.amount').val(num);

        var item = sllw.find('.simple-link-list .list-item:first-child').clone();

        item.removeClass('template');


        sllw.find('.simple-link-list').append(item);
        sllw.find('.order').val(sllw.find('.simple-link-list').sortable('toArray'));


        $(this).closest('.wrapper-widget').children('.wrapper-widget-load').show();

        $(this).closest('form').find('.widget-control-save').trigger('click');
    });

    $('body').on('click.sllw','.moving-handle', function() {
        $(this).parent().find('.sllw-edit-item').slideToggle(200);
    } );
}

function increment_last_num(v) {
    return v.replace(/[0-9]+(?!.*[0-9])/, function(match) {
        return parseInt(match, 10)+1;
    });
}

function updateOrder(self){
    var sllw = self.parents(".widget-content");
    sllw.find('.order').val(sllw.find('.simple-link-list').sortable('toArray'));
}