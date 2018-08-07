(function ($, Drupal) {
  /**
   * @namespace
   */
  Drupal.dataTables = Drupal.dataTables || {};

  /**
   * @type {object.<string, Drupal.dataTables.Datatable>}
   */
  Drupal.dataTables.instances = {};

  /**
   * Helper function that parses a query string into a object.
   *
   * @param query
   * @returns {{}|*}
   * @private
   */
  function _parseQueryString(query) {
    var match,
      urlParams = {},
      pl     = /\+/g,  // Regex for replacing addition symbol with a space
      search = /([^&=]+)=?([^&]*)/g,
      decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); };

    while (match = search.exec(query))
      urlParams[decode(match[1])] = decode(match[2]);

    return urlParams;
  };

  /**
   * Datatable instance object.
   *
   * @param id
   * @param settings
   * @constructor
   */
  Drupal.dataTables.Datatable = function(id, settings) {
    this.id = id;
    this.settings = settings;

    this.$container = $(id);
    this.$dataTable = settings.datatable_selector ? this.$container.find(settings.datatable_selector) : this.$container.find('.view-content > table');

    // Check whether we already have an initialized datatable.
    if ($.fn.dataTable.isDataTable(this.$dataTable)) {
      this.datatable = this.$dataTable.DataTable();
    } else {
      this.datatable = this.$dataTable.DataTable(this.settings);
    }

    this.$dataTable.on('draw.dt', $.proxy(function () {
      Drupal.attachBehaviors(this.$dataTable);
    }, this));

    if (this.settings.bExpandable) {
      this._initializeExpandableRows();
    }

    if (this.settings.exposed_form) {
      this._initializeExposedForm();
    }

    if (this.settings.select) {
      this._initializeSelectPlugin();
    }
  };

  /**
   * Initialize expandable rows.
   *
   * @private
   */
  Drupal.dataTables.Datatable.prototype._initializeExpandableRows = function() {
    var settings = this.settings
      , datatable = this.datatable;

    /* Add event listener for opening and closing details
     * Note that the indicator for showing which row is open is not controlled by DataTables,
     * rather it is done here
     */
    // create new empty tr in freeze column table -> sync height
    // with expandable content
    // -> remove on close
    // css: freezed table needs to be transparent
    // put expandable row *after* visible one
    var detailRows = [];


    this.$dataTable.on('click', 'tr td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = datatable.row(tr);
      var idx = $.inArray(tr.attr('id'), detailRows);

      if (row.child.isShown()) {
        tr.removeClass('details');
        row.child.hide();

        // Remove from the 'open' array
        detailRows.splice(idx, 1);
      }
      else {
        tr.addClass('details');
        row.child(Drupal.theme('datatablesExpandableRow', settings, row.data())).show();

        // Add to the 'open' array
        if (idx === -1) {
          detailRows.push(tr.attr('id'));
        }
      }
    });

    datatable.on('draw', function () {
      $.each(detailRows, function (i, id) {
        $('#' + id + ' td.details-control').trigger('click');
      });
    });
  };

  /**
   * Initialize the exposed form.
   *
   * @private
   */
  Drupal.dataTables.Datatable.prototype._initializeExposedForm = function() {
    this.$containerExposedForm = this.$container.find('.views-exposed-form');
    this.$containerExposedForm.find('input[type=submit], input[type=image], select').once('datatables-ajax').on('input', $.proxy(this.handleExposedFormSubmission, this));
    // Let's make sure this doesn't fire, too often
    this.$containerExposedForm.find('input[type=text]').once('datatables-ajax-keyup').on('keyup',
      Drupal.debounce($.proxy(this.handleExposedFormSubmission, this), 250)
    );

    // Build new url with exposed form values mixed in.
    var new_url = this._buildAjaxUrl(this.datatable.ajax.url(), this._getExposedFormValues());
    // Set url to include any data from the exposed filter (needed for paging).
    this.datatable.ajax.url(new_url);
  };

  /**
   * Retrieves form values from the exposed "form".
   *
   * @returns {{}}
   * @private
   */
  Drupal.dataTables.Datatable.prototype._getExposedFormValues = function() {
    // We cannot rely on $.serialize as we don't have a form ...
    var vals = {};
    this.$containerExposedForm.find('input,select').each(function(){
      if ($(this).attr('name')) {
        vals[$(this).attr('name')] = $(this).val();
      }
    });

    return vals;
  };

  /**
   * Builds a new URL, extracts existing query parameters from the URL, and
   * merges the new values on top.
   *
   * @param url
   * @param vals
   * @returns {string}
   * @private
   */
  Drupal.dataTables.Datatable.prototype._buildAjaxUrl = function(url, vals) {
    // Check if we have a query string
    var queryStringIndex = url.indexOf('?');
    // Base url to build the new url on
    var base_url = (queryStringIndex !== -1) ? url.substring(0, queryStringIndex) : url;
    // Parse query string into an object
    var query = (queryStringIndex !== -1) ? _parseQueryString(url.substring(queryStringIndex + 1)) : {};
    // Merge in exposed "form" values.
    query = $.extend(query, vals);
    // "Serialize" back to url.
    return base_url + '?' + $.param(query);
  };

  /**
   * Handles exposed form submission.
   *
   * @returns {boolean}
   */
  Drupal.dataTables.Datatable.prototype.handleExposedFormSubmission = function() {
    // Build new url with exposed form values mixed in.
    var new_url = this._buildAjaxUrl(this.datatable.ajax.url(), this._getExposedFormValues());
    // Load new url and update datatable.
    this.datatable.ajax.url(new_url).load();
    // Stop propagation and default.
    return false;
  };

  /**
   * Initialize the select plugin
   *
   * @private
   */
  Drupal.dataTables.Datatable.prototype._initializeSelectPlugin = function() {
    this.datatable.on('select' , $.proxy(this._handleSelection, this));
    this.datatable.on('deselect' , $.proxy(this._handleSelection, this));
    this.selectionHandlers = [];
  };

  /**
   *
   * @param id
   */
  Drupal.dataTables.Datatable.prototype.selectRowById = function(id) {
    var that = this;
    // This is highly inefficient :/
    this.datatable.data().each( function (d, idx) {
      var idLabel = that._getIdAndLabelColumnsFromRowData(d);
      if (idLabel.id == id) {
        that.datatable.row(idx).select();
      }
    });
  };

  /**
   * Parses the id and label from row data.
   *
   * @param rowData
   * @returns {{id: String, label: String}}
   * @private
   */
  Drupal.dataTables.Datatable.prototype._getIdAndLabelColumnsFromRowData = function(rowData) {
    // @note: we are *converting* ID & Label to text and trimming it.
    // Drupal *can* add html and comments etc. to it (in theme debug), let's not
    // run into that.
    // @todo: re-add stripping HTML/comments in due course.
    return {
      id: typeof rowData[this.settings.select.id_column] !== 'undefined' ? $.trim(rowData[this.settings.select.id_column]) : null,
      label: typeof rowData[this.settings.select.label_column] !== 'undefined' ? $.trim(rowData[this.settings.select.label_column]) : null
    };
  };

  /**
   * Handle selection event
   *
   * @param e
   * @param dt
   * @param type
   * @param indexes
   * @private
   */
  Drupal.dataTables.Datatable.prototype._handleSelection = function(e, dt, type, indexes) {
    // Row data could be multiples (i.e. multiple selections, that's why we chose
    // the first one *only*.
    var rowData = this.datatable.rows( indexes ).data().toArray()[0];
    var idAndLabel = this._getIdAndLabelColumnsFromRowData(rowData);
    this._triggerRowSelectionEvent(e, dt, type, idAndLabel.id, idAndLabel.label, rowData, indexes);
  };

  /**
   * Trigger row selection event.
   * @todo: this should use some form of proper event lib (backbone or jQuery).
   *
   * @param id
   * @param label
   * @param rowData
   * @param e
   * @private
   */
  Drupal.dataTables.Datatable.prototype._triggerRowSelectionEvent = function(e, dt, type, id, label, rowData, indexes) {
    $.each(this.selectionHandlers, function(index, selectionHandler) {
      selectionHandler(e, dt, type, id, label, rowData, indexes);
    });
  };

  /**
   * Register a selection listener
   *
   * @param callback
   */
  Drupal.dataTables.Datatable.prototype.registerSelectionListener = function(callback) {
    this.selectionHandlers.push(callback);
  };

  Drupal.dataTables.Datatable.prototype.setSelectionValue  = function(id) {
    this.selectRowById(id);
  };

  /**
   * (Static) factory function that returns either an existent instance or
   *
   * @param id
   * @param settings
   * @returns {Drupal.dataTables.Datatable}
   */
  Drupal.dataTables.Datatable.createInstance = function(id, settings) {
    if (typeof Drupal.dataTables.instances[id] === 'undefined') {
      Drupal.dataTables.instances[id] = new Drupal.dataTables.Datatable(id, settings);
    }
    return Drupal.dataTables.instances[id];
  };

  /**
   * Retrieve a datatable instance via Views DOM ID.
   *
   * @param id
   * @returns {Drupal.dataTables.Datatable|null}
   */
  Drupal.dataTables.Datatable.getInstanceByViewDomId = function(id) {
    // @todo: refactor this into the appropriate place (this is too views
    // specific.
    id = '.js-view-dom-id-' + id;
    return this.getInstanceById(id);
  };

  /**
   * Retrieve a datatable instance via Views DOM ID.
   *
   * @param id
   * @returns {Drupal.dataTables.Datatable|null}
   */
  Drupal.dataTables.Datatable.getInstanceById = function(id) {
    if (typeof Drupal.dataTables.instances[id] !== 'undefined') {
      return Drupal.dataTables.instances[id];
    }
    return null;
  };
})(jQuery, Drupal);
