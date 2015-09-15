  (function( $ ) {
    $.widget( "custom.combobox", {
      options:{
          size:"",
          clear:false,
          blurOnSelect:true,
          selectOnFocus:true,
      },
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
        var left,right;
        left = this.input[0].getBoundingClientRect().left;
        right = this.toggleButton[0].getBoundingClientRect().right;
        this.wrapper.width(right - left);
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        var size=this.options.size;
        if (size == "auto") {
          size=0;
          this.element.children("option").each(function(){
            if(!size) size = this.text.length;
            else size = Math.max(size, this.text.length);
          });
        }
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .attr("size", size)
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
        if (this.options.selectOnFocus) {
          (function(input){
            var selected = false;
            input.click(function(){
              if (!selected) {
                this.setSelectionRange(0, this.value.length);
                selected = true;
              }
            }).blur(function(){
              selected = false;
            });
          })(this.input);
        }
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
            if (this.options.blurOnSelect) {
              this.input.val(ui.item.label);
              this.input.blur();
            }
            this.element.change();
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
        var _this = this;

        var clearButton;
        if (this.options.clear) {
          clearButton = $("<a>")
            .appendTo( this.wrapper )
            .button({
              icons: {
                primary: "ui-icon-closethick"
              },
              text: false
            })
            .removeClass("ui-corner-all")
            .addClass("custom-combobox-toggle")
            .click(function(){
              var selectedIndex = _this.element.prop("selectedIndex");
              var option = _this.element.children("option").first();
              var change = false;
              if (selectedIndex != 0) {
                _this.element.prop("selectedIndex", 0);
                change = true;
              }
              _this.input.val(option.text());
              if (change) {
                _this.element.change();
              }
            })
          ;
        }
 
        this.toggleButton=
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
        if (this.options.clear) {
          this.toggleButton.css("margin-left", clearButton.outerWidth()-2);
        }
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          this.element.change();
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        var element = this.element;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            element.change();
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this.element.change()
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
