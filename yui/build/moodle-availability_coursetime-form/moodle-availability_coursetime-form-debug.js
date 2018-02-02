YUI.add('moodle-availability_coursetime-form', function (Y, NAME) {

/**
 * JavaScript for form editing course conditions.
 *
 * @module moodle-availability_coursetime-form
 */
M.availability_coursetime = M.availability_coursetime || {};

/**
 * @class M.availability_coursetime.form
 * @extends M.core_availability.plugin
 */
M.availability_coursetime.form = Y.Object(M.core_availability.plugin);

/**
 * Groupings available for selection (alphabetical order).
 *
 * @property courses
 * @type Array
 */
M.availability_coursetime.form.courses = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} standardFields Array of objects with .field, .display
 * @param {Array} customFields Array of objects with .field, .display
 */
M.availability_coursetime.form.initInner = function(standardFields) {
    this.courses = standardFields;
};

M.availability_coursetime.form.getNode = function(json) {
    // Create HTML structure.
    var strings = M.str.availability_coursetime;
    var html = '<span class="availability-group">';
    html += '<label><span class="accesshide"></span> ' + strings.conditiontitle +
            ' <input name="timespent" type="text" style="width: 10em" title="' +
            strings.conditiontitle + '"/></label>';
    html += '<label> ' + strings.incourse + ' ' +
            '<select name="courseid">' +
            '<option value="choose">' + M.str.moodle.choosedots + '</option>';
    var fieldInfo;
    for (var i = 0; i < this.courses.length; i++) {
        fieldInfo = this.courses[i];
        // String has already been escaped using format_string.
        html += '<option value="c_' + fieldInfo.field + '">' + fieldInfo.display + '</option>';
    }
    html += '</select></label>';
    html += '</span>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values if specified.
    if (json.c !== undefined &&
            node.one('select[name=courseid] > option[value=c_' + json.c + ']')) {
        node.one('select[name=courseid]').set('value', 'c_' + json.c);
    }
    if (json.t !== undefined) {
        node.one('input[name=timespent]').set('value', json.t);
    }

    // Add event handlers (first time only).
    if (!M.availability_coursetime.form.addedEvents) {
        M.availability_coursetime.form.addedEvents = true;
        var updateForm = function(input) {
            var ancestorNode = input.ancestor('span.availability_coursetime');
            M.core_availability.form.update();
        };
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
             updateForm(this);
        }, '.availability_coursetime select');
        root.delegate('change', function() {
             updateForm(this);
        }, '.availability_coursetime input[name=timespent]');
    }

    return node;
};

// This brings back form values into an exportable object
M.availability_coursetime.form.fillValue = function(value, node) {
    // Set field.
    var field = node.one('select[name=courseid]').get('value');
    if (field.substr(0, 2) === 'c_') {
        value.c = field.substr(2);
    }

    var valueNode = node.one('input[name=timespent]');
    value.t = valueNode.get('value');
};

M.availability_coursetime.form.fillErrors = function(errors, node) {

    var value = {};
    this.fillValue(value, node);

    // Check profile item id.
    if (value.t === undefined) {
        errors.push('availability_coursetime:error_nulltimespent');
    }
    if (value.c === undefined) {
        errors.push('availability_coursetime:error_nocourse');
    }
};

}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
