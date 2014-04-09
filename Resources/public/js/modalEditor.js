// This function will use the inline editor
// TODO: build in preview support for inline editor like with the modal editor (OK button and re-render pagepart?)
function useInlineEditor(id) {
    // disable the CKEditors
    // This has to be done every time the editor type is switched because if not re-enabled, the CKEditor won't work
    disableCKEditors();

    // check if inline editor is alredy in use
    if (!$('#submit_edit_'+id).attr('data-toggle')) {
        return PagePartEditor.editPagepart(id);
    }

    // remove attributes from the edit button
    $('#submit_edit_'+id).removeAttr('data-toggle');
    $('#submit_edit_'+id).removeAttr('data-target');

    // set the content of the edit view to an inline editor
    $('#'+id+'_edit').html($('#edit-pagepart-modal'+ id +' .modal-body').html());

    // re-enable the CKEditors
    enableCKEditors();
}

// This function will change the default editor of a pagepart to a modal editor.
function useModalEditor(id) {
    // check if modal editor is already in use
    if ($('#submit_edit_'+id).attr('data-toggle')) {
        return PagePartEditor.editPagepart(id);
    }

    // change the edit button attributes and behaviour
    $('#submit_edit_'+id).attr('data-toggle', "modal");
    $('#submit_edit_'+id).attr('data-target', "#edit-pagepart-modal"+id);
    $('#submit_edit_'+id).click(function() {
        var parent = $('#'+id+'_view');
        parent.attr('style', '');
    });

    // some data
    var parent = $('#'+id+'_edit');
    content = parent.html();
    pagepartType = $('#pagepart_'+id).attr('data-pagepart-type');

    // set the content of the edit view to a modal editor
    var newContent = ''
                + '<div id="edit-pagepart-modal'+id+'" class="modal modal--edit hide fade">'
                + '<div class="modal-header">'
                +    '<button class="close" data-dismiss="modal">&times;</button>'
                +    "<h3>Edit '"+pagepartType+"'</h3>"
                + '</div>'
                + '<div class="modal-body">'
                + content
                + '</div>'
                + '<div class="modal-footer">'
                +    '<div class="btn_group">'
                +        '<button type="button" id="" onclick="closeModalEditor('+id+')" class="btn btn-primary">Ok</button>'
                +    '</div>'
                + '</div>'
                + '</div>';
    parent.html(newContent);

    // re-enable the CKEditors
    // This has to be done every time the editor type is switched because if not re-enabled, the CKEditor won't work
    enableCKEditors();
    $('#form_pagepartadmin_'+ id +' .input .cke_ltr:last-child').remove();
}

// This function will close the modal editor.
function closeModalEditor(id) {
    // on closing the modal editor, the pagepart has to re-render with the updated information
    renderPagepart(id);

    // hide the modal editor
    $('#edit-pagepart-modal'+id).modal("hide");
}

// This function will take the data from the modal form and make an ajax call to render a pagepart template with the new data.
// This data is then inserted back into the pagepart preview. It's not saved however untill the user presses the "save" button.
function renderPagepart(id) {
    // get the pagepart type
    var pagepartType = $('#pagepart_'+id).attr('data-pagepart-type');

    // empty JSON object
    var content = {};

    // set the type and add an empty data JSON object
    content.type = pagepartType;
    content.data = [];

    // get data from normal input fields
    $('#edit-pagepart-modal'+id+' *:input').each(function(){
        if ($(this).val()) {
            content.data.push({
                name: $(this).attr('id'),
                value: $(this).val()
            });
        }
    });

    // get data from iframe CKE editor
    var ckeValue = $('#edit-pagepart-modal'+id+' #cke_iframe_content').contents().find('.cke_editable').html()
    if (ckeValue) {
        content.data.push({
            name: 'content',
            value: ckeValue
        });
    }

    // Create the AJAX request
    $.ajax({
        type: 'POST',
        url: 'renderPagepart/',
        data: content,
        dataType: "html",
        success: function(response) {
            // get the pagepart preview div
            var view = $('#'+id+'_view');

            // Some checks here: a header pagepart is inserted directly in the preview div, link and text are rendered in a sub-div or paragraph.
            if (view.children().children().length > 0) {
                // if 2-level view: insert the rendered template in the lowest
                $('#'+id+'_view > * > *:first-child').html(response);
            } else if (view.children().length > 0) {
                // if 1-level view: insert the rendered template there
                $('#'+id+'_view > *:first-child').html(response);
            } else {
                // if no children: just insert the rendered template directly in the view div
                view.html(response);
            }
            // opening the modal editor hides the preview div, so show it again
            view.attr('style', '');
        }
    });
}
