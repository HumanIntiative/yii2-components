
// Real Vars
var content;
var fnCreatedRow = function ( row, data, index ) {
    jQuery('td', row).eq(0).html(index+1)
    jQuery('td', row).eq(0).css('width', '5px')
    jQuery('td', row).eq(0).addClass('kv-align-center kv-align-middle')
    for (var i = 1; i < delIndex; i++) {
        content = jQuery('td', row).eq(i).html().replace(/\[0\]/gi, '['+index+']').replace(/\-0\-/gi, '-'+index+'-')
        jQuery('td', row).eq(i).html(content)
    }
    jQuery('td', row).eq(delIndex).addClass('kv-align-center kv-align-middle')

    setTimeout(createdCallback(row, data, index), 50);
};
var varTable = jQuery( divSelector + ' table.kv-grid-table' ).DataTable({
    'dom': 't',
    'ordering': false,
    'paging': false,
    'createdRow': fnCreatedRow
});
var varRowTable = varTable.row( divSelector + ' tr.kv-tabform-row:first-child' );
var fnInputClearValue = function(match, p1, p2, p3, offset, string) {
    return '<'+[p1, 'value=\"\"', p3].join(' ')+'>';
};
var fnAddingRow = function (e) {
    var columns = varRowTable.data(),
        replacer = 'value=\"\"',
        regexInputId = /\<(input type=\"hidden\".*)value=\"([^\"]*)\"\>/g,
        regexInputIdAlt = /\<(input.*)value=\"([^\"]*)\" (type=\"hidden\")\>/g,
        regexBudgetId = /\<input id=\"budget_id(.+?)\>/g,
        regexBudgetName = /\<input id=\"budget_name(.+?)\>/g,
        regexTexarea = /<textarea(.*?)>(.*?)<\/textarea>/g,
        regexInput = /\<(input type=\"text\".*)value=\"(.+)\"\>/g,
        regexInputAlt = /\<(input.*)value=\"(.+)\" (type=\"text\")\>/g

    columns[delIndex] = columns[delIndex].replace('hidden', '')
    for (var i=0; i <= delIndex; i++) {
        // for id
        if (regexInputId.test(columns[i])) {
            columns[i] = columns[i].replace(regexInputId, fnInputClearValue)
        }
        if (regexInputIdAlt.test(columns[i])) {
            columns[i] = columns[i].replace(regexInputIdAlt, fnInputClearValue)
        }

        // for budget_id
        if (regexBudgetId.test(columns[i])) {
            columns[i] = columns[i].replace(regexBudgetId, '')
        }
        if (regexBudgetName.test(columns[i])) {
            columns[i] = columns[i].replace(regexBudgetName, '')
        }

        // for textarea
        if (regexTexarea.test(columns[i])) {
            columns[i] = columns[i].replace(regexTexarea, '<textarea $1></textarea>')
        }

        // for other input
        if (regexInput.test(columns[i])) {
            columns[i] = columns[i].replace(regexInput, fnInputClearValue)
        }
        if (regexInputAlt.test(columns[i])) {
            columns[i] = columns[i].replace(regexInputAlt, fnInputClearValue)
        }
    }
    varTable.row.add( columns ).draw( false )
};
var fnDeletingRow = function(e) {
    e.preventDefault()
    varTable.row( $(this).parents('tr') ).remove().draw()
}

// Events
jQuery( divSelector ).on( 'click', addSelector, fnAddingRow );
jQuery( divSelector + ' table.kv-grid-table tbody').on( 'click', delSelector, fnDeletingRow );