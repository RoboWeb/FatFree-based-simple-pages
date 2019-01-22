/* ----------------------------- contact form - flyForm ------------------------ */
(function(){
  'use strict';
  // v.1.2 - 2016-05-31
  var flyForm = new Vue({
    el: "#contactForm",
    http: {
      emulateJSON: true
    },
    data: {
      fields: '',
      settings: '',
      notification: {
        status: 0,
        header: "Błąd:",
        body: ""
      },
      values: [],
      statements: [],
      btnSubmitStatus: 0,
      /* 0 - default, 1 - when form submitted, 2 - when success, 3 - when error */
      timeoutId: ''
    },
    compiled: function(){
      // get item
      this.$http({url: this.$el.getAttribute("action"), method: 'GET'}).then(function(response){
        this.$set('fields', response.data.fields);
        this.$set('settings', response.data.general);
        //console.log(this.fields);
        this._fieldsLoaded();
      }, function(response){
        console.error('!OK', response);
      });
    },
    ready: function(){

    },
    methods: {
      submitForm: function(){
        // console.log('submitForm');
        var t = this;
        // var arrVals = [];
        var data = {};
        for(var f in t.fields){
          //console.log(t.fields[f].id, 'value: ' + (t.fields[f].value ? t.fields[f].value : t.fields[f].values), 'isTextype: ' + t._isTextType(t.fields[f]), 'isRequire: ' +  t._isRequire(t.fields[f]), 'isEmpty: ' + t._isEmpty(t.fields[f]), t._isRequire(t.fields[f]) && t._isEmpty(t.fields[f]) ? "empty": "ok");
          if(t._isRequire(t.fields[f]) && t._isEmpty(t.fields[f])) t._setFieldError(t.fields[f], 'empty');
          else if(!t._isEmpty(t.fields[f]) && !t._isValid(t.fields[f])) t._setFieldError(t.fields[f], 'invalid');

          if(t.fields[f].type === 'checkbox') data[t.fields[f].id] = t.fields[f].value ? "TAK" : "NIE";
          if(t.fields[f].type === 'multicheckbox') {
            var arrVals = [];
            for(var c in t.fields[f].checkboxes)
              if(t.fields[f].checkboxes[c].checked === true)
                arrVals.push(t.fields[f].checkboxes[c].value);
            t.fields[f].values = arrVals;
            data[t.fields[f].id] = t.fields[f].values;
          }
          if(t._isTextType(t.fields[f])) data[t.fields[f].id] = t.fields[f].value;
        }
        if(t.statements.length > 0) {
          t._showNotify();
          return;
        }
        // console.log('fields are valid', data);
        t.$http.post(t.$el.getAttribute("action"), data).then(function(response){
          t.btnSubmitStatus = 1;
          if(response.data == 100) {
            t.btnSubmitStatus = 2;
            t._setSuccessNotify();
            t._showNotify(2);
          } else {
            t.btnSubmitStatus = 3;
            if(({300:1, 310:1, 320:1, 340:1})[response.data.id])
              t._setGeneralError(response.data.id, response.data.cont);
            else if (({330:1})[response.data.id]) {
              for(var f in t.fields) {
                for(var i in response.data.stats) {
                  if(response.data.stats[i].name === t.fields[f].id) t._setFieldError(t.fields[f], 'invalid');
                }
              }
            }
            t._showNotify();
          }
        }, function(response){
          console.error('!OK', response);
        });

        setTimeout(function(){
          t.btnSubmitStatus = 0;
        }, t.settings.notificationTime * 1000);
      },

      _fieldsLoaded: function(){
        var f = this.fields;
        var i = 1;
        for(var p in f){
          if(this._isTextType(f[p])) {
            f[p].el = document.getElementById(f[p].id);
            this._setFocusActions(f[p]);
            f[p].el.setAttribute('tabindex', (f[p].tabindex ? f[p].tabindex : i));
          }
          if(({checkbox:1, radio:1})[f[p].type]) {
            f[p].el = document.getElementById(f[p].id);
            this._setCheckRadActions(f[p]);
            f[p].el.setAttribute('tabindex', (f[p].tabindex ? f[p].tabindex : i));
          }
          if(f[p].type === 'multicheckbox') {
            for(var c in f[p].checkboxes) {
              var id = f[p].id + '_' + c;
              f[p].checkboxes[c].el = document.getElementById(id);
              f[p].checkboxes[c].el.addEventListener('click', function(ev){
                f[p].error = false;
              });
              f[p].checkboxes[c].el.setAttribute('tabindex', (f[p].tabindex ? f[p].tabindex : i));
            }
          }
          if(f[p].require) this._setRequireAction(f[p]);
          i++;
        }
      },
      _setFocusActions: function(field){
        field.el.addEventListener('focus', function(ev){
          var target = ev.target || ev.srcElement;
          target.parentNode.classList.add('focus');
          if(field.placeholder) field.el.setAttribute('placeholder', field.placeholder);
          field.el.classList.remove('empty', 'invalid');
        });
        field.el.addEventListener('blur', function(ev){
          var target = ev.target || ev.srcElement;
          target.parentNode.classList.remove('focus');
        });
      },
      _setRequireAction: function(field){
        var t = this;
        field.el.addEventListener('blur', function(ev){
          field.error = false;
        });
      },
      _setCheckRadActions: function(field){
        var t = this;

        field.el.addEventListener('click', function(ev){
          if (t._isRequire(field) && !t._isValid(field)) {
            field.error = true;
          } else {
            field.error = false;
          }
        });
      },
      _isRequire: function(f){
        return !!f.require;
      },
      _isEmpty: function(f){
        if(this._isTextType(f)) return !f.value;
        else return;
      },
      _isTextType: function(f){
        return !!({text:1, name:1, phone:1, email:1, url:1, textarea:1})[f.type];
      },
      _isValid: function(f){
        if(f.type === 'checkbox') return typeof f.valid !== 'undefined' ? f.valid === f.value : true;
        if(f.type === 'multicheckbox') return true; //todo: test for valid: min checked positions for valid
        var re = this._getPattern(f.type);
        return re.test(f.value) &&
        (f.value.length >= (typeof f.charsNumber.min !== 'undefined' ? f.charsNumber.min : 0) &&
        f.value.length <= (typeof f.charsNumber.max !== 'undefined' ? f.charsNumber.max : 120));
      },
      _setFieldError: function(field, type){
        field.error = true;
        this.statements.push({id: field.id, label: field.label, cont: type === 'empty' ? field.errStatements.empty : field.errStatements.invalid});
      },
      _setGeneralError: function(errNo, statmnt){
        this.statements.push({
          id: errNo,
          cont: statmnt
        });
      },
      _setSuccessNotify: function(){
        this.statements.push({cont: this.settings.successStatement.body});
        this._resetForm();
      },
      _showNotify: function(status){
        status = typeof status !== 'undefined' ? status :  3;
        var note = this.notification;
        note.status = status;
        if(status === 3) {
          if(this.statements.length > 1){
            if(this.statements.length > 5)
              note.header = "Mamy tu sporo błędów (dokładnie " + this.statements.length + "). Może zaczniemy od poprawienia poniższych?";
            else note.header = "Mamy kilka błędów: ";
          } else note.header = "Błąd:";
        }
        else note.header = this.settings.successStatement.header;
        note.body = this.statements.slice(0,5);
        // console.log('statements = ', this.statements.length);
        this.timeoutId = window.setTimeout(this.closeNotification, this.settings.notificationTime * 1000 * this.statements.length);
      },
      _resetForm: function(){
        var t = this;
        for(var f in t.fields) {
          if(t.fields[f].type === 'multicheckbox') {
            t.fields[f].values = [];
            for(var c in t.fields[f].checkboxes){
              t.fields[f].checkboxes[c].checked = (typeof t.fields[f].checkboxes[c].default !== 'undefined' ? t.fields[f].checkboxes[c].default :false);
            }
          } else if (t.fields[f].type === 'checkbox') {
            t.fields[f].checked = (typeof t.fields[f].default !== 'undefined' ? t.fields[f].default : false);
          } else t.fields[f].value = '';
        }
      },
      closeNotification: function(){
        this.notification.status = 0;
        this.notification.body = "";
        this.statements = [];
        window.clearTimeout(this.timeoutId);
      },
      _getPattern: function(type){
        var patterns = {
          'name' : /^(([\w-ĄĆĘŁÓŚŹŻąćęłńóśźż]+)\s([\w-ĄĆĘŁÓŚŹŻąćęłńóśźż]+)){1,2}$/,
          'phone': /^(\+\d{2}\ {0,1}){0,1}(\({0,1}\d{2,3}\){0,1}\ {0,1}){0,1}(\d{2,3}(\ |\-){0,1}){3}$/,
          'email': /^(\w+\.{0,}\-{0,}){1,}@{1}(\w+\.{0,}\-{0,}){1,}(\.{1}\w{2,3}){1}$/,
          'url': /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/,
          'text': /^([\w-ąćęłńóśźż\s\(\).,:]+)$/,
          'textarea': /^([\w-ĄĆĘŁÓŚŹŻąćęłńóśźż\s\d_\\\/\'\"\&\(\)\,\.\:]+)$/
        }
        return patterns[type];
      }
    }
  });
})();
