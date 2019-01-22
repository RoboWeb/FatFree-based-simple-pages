/* ----------------------------- contact form - flyForm ------------------------ */
(function(){
  'use strict';
 // v = 1.0
  var flyForm = new Vue({
    el: "#contactForm",
    http: {
      root: sUrl,
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
      statements: [],
      btnSubmitStatus: 0,
      /* 0 - default, 1 - when form submitted, 2 - when success, 3 - when error */
      timeoutId: ''
    },
    compiled: function(){
      // get item
      this.$http({method: 'GET'}).then(function(response){
        this.$set('fields', response.data.fields);
        this.$set('settings', response.data.general);
        this._fieldsLoaded(this.fields);
      }, function(response){
        console.error('!OK', response);
      });
    },
    ready: function(){
      console.log("Ready to Send");
    },
    methods: {
      submitForm: function(){
        // console.log('submitForm');
        var t = this;

          var data = {};
          for(var f in t.fields) {
            if(!t._isValid(t.fields[f])) {
              t._setFieldError(t.fields[f], 'invalid');
            }
            data[t.fields[f].id] = t.fields[f].value;
          }
          if(t.statements.length > 0) {
            t._showNotify();
            return;
          }
          // console.log('fields are valid');
          t.$http.post(sUrl, data).then(function(response){
            // console.log(response);
            if(response.data == 100) {
              t._setSuccessNotify();
              t._showNotify(2);
            } else {
              switch(response.data.id) {
                case 300:
                case 310:
                case 320:
                case 340:
                  t._setGeneralError(response.data.id, response.data.cont);
                  break;
                case 330:
                  for(var f in t.fields) {
                    for(var i in response.data.stats) {
                      if(response.data.stats[i].name === t.fields[f].id) t._setFieldError(t.fields[f], 'invalid');
                    }
                  }
                  break;
              }
              t._showNotify();
            }
          }, function(response){
            console.error('!OK', response);
          });

        setTimeout(function(){
          t.btnSubmitStatus = 0;
        }, t.settings.notificationTime * 2 * 1000);
      },

      _fieldsLoaded: function(f){
        for(var p in f){
          switch(f[p].type){
            case 'text':
            case 'name':
            case 'phone':
            case 'email':
            case 'textarea':
            f[p].el = document.getElementById(f[p].id);
            this._setFocusActions(f[p]);
            break;
            case 'checkbox':
            case 'radio':
            f[p].el = document.getElementById(f[p].id);
            this._setCheckRadActions(f[p]);
            break;
          }
          if(f[p].require) this._setRequireAction(f[p]);
        }
      },
      _setFocusActions: function(field){
        field.el.addEventListener('focus', function(ev){
          var target = ev.target || ev.srcElement;
          target.parentNode.classList.add('focus');
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
          if(field.value === '') {
            t._setFieldError(field, 'empty');
            t._showNotify();
          } else if (!t._isValid(field)) {
            t._setFieldError(field, 'invalid');
            t._showNotify();
          } else field.error = false;
        });
      },
      _setCheckRadActions: function(field){
        var t = this;
        field.el.addEventListener('click', function(ev){
          field.error = false;
        });
      },
      _isValid(f){
        if(f.type === 'checkbox') return typeof f.valid !== 'undefined' ? f.valid === f.checked : true;
        var re = this._getPattern(f.type);
        return re.test(f.value) &&
        (f.value.length >= (typeof f.charsNumber.min !== 'undefined' ? f.charsNumber.min : 0) &&
        f.value.length <= (typeof f.charsNumber.max !== 'undefined' ? f.charsNumber.max : 120));
      },
      _setFieldError: function(field, type){
        field.error = true;
        this.statements.push({id: field.id, label: field.label, cont: type === 'empty' ? field.errStatements.empty : field.errStatements.invalid});
        //console.log('statemetns:',this.statements);
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
        status = typeof status !== 'undefined' ? status : 3;
        var note = this.notification;
        note.status = status;
        if(status === 3) note.header = this.statements.length > 1 ? "Jest kilka błędów:" : "Błąd:";
        else note.header = this.settings.successStatement.header;
        note.body = this.statements;
        // console.log('statements = ', this.statements.length);
        this.timeoutId = window.setTimeout(this.closeNotification, this.settings.notificationTime * 1000 * this.statements.length);
      },
      _resetForm: function(){
        var t = this;
        for(var f in t.fields) {
          t.fields[f].value = '';
        }
      },
      closeNotification: function(){
        this.notification.status = 0;
        this.notification.body = "";
        this.statements = [];
        window.clearTimeout(this.timeoutId);
      },
      _getPattern: function(type){
        switch(type){
          case 'name':
          return /^(([\w-ąćęłńóśźż]+)\s([\w-ąćęłńóśźż]+)){1,2}$/;
          break;
          case 'phone':
          return /^(\+\d{2}\ {0,1}){0,1}(\({0,1}\d{2,3}\){0,1}\ {0,1}){0,1}(\d{2,3}(\ |\-){0,1}){3}$/;
          break;
          case 'email':
          return /^(\w+\.{0,}\-{0,}){1,}@{1}(\w+\.{0,}\-{0,}){1,}(\.{1}\w{2,3}){1}$/;
          break;
          case 'text':
          case 'textarea':
          return /^([\w-ąćęłńóśźż\s\(\).,:]+)$/;
          break;

        }
      }

    }
  });
})();
