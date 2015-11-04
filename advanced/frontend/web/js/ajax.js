/**
 * @link http://yiiplayground.com/
 * @copyright Copyright (c) 2014 Giovanni Derks & Yii Playground contributors
 * @license http://opensource.org/licenses/BSD-3-Clause
 */


function handleAjaxLink (e) {

    e.preventDefault();

    var
        $link = $(e.target),
        callUrl = $link.data('href'),
        formId = $link.data('formId'),
        onDone = $link.data('onDone'),
        onFail = $link.data('onFail'),
        onAlways = $link.data('onAlways'),
        ajaxRequest;


    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
    });

    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    }

    // Assign fail handler
    if (typeof onFail === "string" && ajaxCallbacks.hasOwnProperty(onFail)) {
        ajaxRequest.fail(ajaxCallbacks[onFail]);
    }

    // Assign always handler
    if (typeof onAlways === "string" && ajaxCallbacks.hasOwnProperty(onAlways)) {
        ajaxRequest.always(ajaxCallbacks[onAlways]);
    }

}

function validate (response) {
    console.log(response, typeof(response));

    if (typeof (response) != 'object' || response.code != 0) {

        $.notify({
            // options
            icon: 'glyphicon glyphicon-warning-sign',
            title: response.message,
            message: ''
        }, {
            // settings
            type: 'danger'
        });

        return false;
    } else {
        return true;
    }
}


var ajaxCallbacks = {

    'simpleDone': function (response) {
        // This is called by the link attribute 'data-on-done' => 'simpleDone'
        console.dir(response);
        $('#ajax_result_01').html(response.body);
    },


    'afterAnswerCreateSuccess': function (response) {
        // This is called by the link attribute 'data-on-done' => 'linkFormDone';
        // the form name is specified via 'data-form-id' => 'link_form'
        if (validate(response)) {
            $('#answer_item_area').append(response.data.answer_item);
            $('#answer_form_area').html(response.data.answer_form);
        }
    }

}