
// This function will close the modal editor.
function closeModal(id) {
    // on closing the modal editor, the pagepart has to re-render with the updated information
    renderPagepart(id);
    // hide the modal editor
    $('#edit-pagepart-modal'+id).modal("hide");
}

// This function will change the default editor of a pagepart to a modal editor.
function useModal(id) {
    // change the edit button attributes and behaviour
    $('#submit_edit_'+id).attr('data-toggle', "modal");
    $('#submit_edit_'+id).attr('data-target', "#edit-pagepart-modal"+id);
    $('#submit_edit_'+id).click(function() {
        var parent = $('#'+id+'_view');
        parent.attr('style', '');
    });

    // some data
    var parent = $('#'+id+'_edit');
    var data = {};
    data.id = id;
    data.content = parent.html();
    data.pagepartType = $('#pagepart_'+id).attr('data-pagepart-type');

    // edit the pagepart editor to a modal editor
    var newContent = ''
                + '<div id="edit-pagepart-modal'+data.id+'" class="modal modal--edit hide fade">'
                + '<div class="modal-header">'
                +    '<button class="close" data-dismiss="modal">&times;</button>'
                +    "<h3>Edit '"+data.pagepartType+"'</h3>"
                + '</div>'
                + '<div class="modal-body">'
                + data.content
                + '</div>'
                + '<div class="modal-footer">'
                +    '<div class="btn_group">'
                +        '<button type="button" id="" onclick="closeModal('+data.id+')" class="btn btn-primary">Ok</button>'
                +    '</div>'
                + '</div>'
                + '</div>';
    parent.html(newContent);
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
    content.data = {};

    // This has to be hardcoded: every pagepart has other properties, and somewhere they have to be seperatly added.
    switch (pagepartType.toLowerCase()) {
        case 'text':
            content.data.content = $('#cke_iframe_content').contents().find('.cke_editable').html();
            break;

        case 'link':
            content.data.url = $('#form_pagepartadmin_'+id+'_url').val();
            content.data.openinnewwindow = $('#form_pagepartadmin_'+id+'_openinnewwindow').val();
            content.data.text = $('#form_pagepartadmin_'+id+'_text').val();
            break;

        case 'header':
            content.data.niv = $('#form_pagepartadmin_'+id+'_niv').val();
            content.data.title = $('#form_pagepartadmin_'+id+'_title').val();
            break;

        default: // add default behaviour if unknown pagepart?
            break;
    }

    // Create the AJAX request
    $.ajax({
        type: 'POST',
        url: 'renderPagepart/',
        data: content,
        dataType: "html",
        success: function(response) {
            // get the pagepart preview div
            var parent = $('#'+id+'_view');
            // Some checks here: a header pagepart is inserted directly in the preview div, link and text are rendered in a sub-div or paragraph.
            if (parent.children().children().length > 0) { // if 2-level view: insert the rendered template in the lowest
                $('#'+id+'_view > * > *:first-child').html(response);
            } else if (parent.children().length > 0) { // if 1-level view: insert the rendered template there
                $('#'+id+'_view > *:first-child').html(response);
            } else {
                parent.html(response); // if no children: just insert the rendered template directly in the view div
            }
            parent.attr('style', ''); // opening the modal editor hides the preview div, so show it again
        }
    });
}
