import $ from "jquery";

export default function (modal_selector) {
    $(modal_selector).on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let modal = $(this);
        modal.find('.modal-body')
            .empty()
            .append($('<span />', {}).html('Loading...'))
            .append($('<img />', {src: button.data('image')}).on('load', function () {
                modal.find('.modal-body').children('span').remove();
            }));
    });
};