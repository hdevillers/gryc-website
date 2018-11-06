/*
 *    Copyright 2015-2018 Mathieu Piot
 *    Copyright 2018 Hugo Devillers
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

(function ($) {
  $(document).ready(function(){
    var submit = $( 'form[name=registration] > button[type=submit],'+
                    ' form[name=reset_password] > button[type=submit]'
                  );
    submit.prop('disabled', true);

    // Point input password objects
    var password1 = $( "[id$='_plainPassword_first']" );
    var password2 = $( "[id$='_plainPassword_second']" );

    // Disable input password 2
    password2.prop('disabled', true);

    /* Control password1:
      - If validated, then enable password2 field,
      - else, empty and disable password2
    */
    password1.keyup( function() {
      var valid = true;
      var rules = Array(
        'number-chars', 'upper-case',
        'lower-case', 'number'
      );
      var regex = Array(
        new RegExp("^.{8,}$"), new RegExp("[A-Z]+"),
        new RegExp("[a-z]+"), new RegExp("[0-9]+")
      );

      for(var i=0; i<rules.length; i++) {
        // Capture the object
        var elem = $('#'+rules[i]);
        if(regex[i].test(password1.val())) {
          elem.removeClass('fa-times');
          elem.addClass('fa-check');
          elem.css('color', '#00A41E');
        } else {
          elem.removeClass('fa-check');
          elem.addClass('fa-times');
          elem.css('color', '#FF0004');
          valid = false;
        }
      }

      // Editing password1 alway empty password2
      if(password1.val() !== password2.val()) {
        password2.val('');
        var elem = $('#password-match');
        elem.removeClass('fa-check');
        elem.addClass('fa-times');
        elem.css('color', '#FF0004');
        // As well as submit button
        submit.prop('disabled', true);
      }

      if(valid) {
        // Enable password2
        password2.prop('disabled', false);
      } else {
        // Disable password2
        password2.prop('disabled', true);
        submit.prop('disabled', true);
      }
    });

    /* Control password2
    - If validated, enable submit button
    - else, enable it.
    */
    password2.keyup( function() {
      var elem = $('#password-match');
      if(password1.val() === password2.val() && password1.val() !== '') {
        elem.removeClass('fa-times');
        elem.addClass('fa-check');
        elem.css('color', '#00A41E');
        // Enable submit button
        submit.prop('disabled', false);
      } else {
        elem.removeClass('fa-check');
        elem.addClass('fa-times');
        elem.css('color', '#FF0004');
        // Disable submit button
        submit.prop('disabled', true);
      }
    });
  });
})(jQuery);
