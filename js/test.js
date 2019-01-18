$(document).ready( function () {
  var  sel_el_type = $('#sel2'),
    sel_base = $('#sel1'),
    sel_note_type = $('#sel3'),
    sel_task_type = $('#sel4'),
    inp_textar1 = $('#textar1'),
    inp_date1 = $('#date1'),
    inp_phone1 = $('#phone1'),
    inp_num1 = $('#num1'),
    inp_but1 = $('#but1'),
    req_url;
  $('#sel1 option:eq(1)').prop('selected', true);
  sel_base.prop('selected', true);
  sel_el_type.hide();
  sel_note_type.hide();
  sel_task_type.hide();
  inp_textar1.hide();
  inp_date1.hide();
  inp_phone1.hide();
  sel_note_type.change( function () {
    if (sel_note_type.find(":selected").val() === '4') {
      inp_textar1.show();
      inp_phone1.hide();
    } else {
      inp_textar1.hide();
      inp_phone1.show();
    }
  });
  sel_base.change( function () {
    switch (sel_base.find(":selected").val()) {
      case '1':
        sel_el_type.hide();
        sel_note_type.hide();
        sel_task_type.hide();
        inp_textar1.hide();
        inp_date1.hide();
        inp_phone1.hide();
        inp_num1.attr("placeholder", 'количество элементов');
        break;

      case '2':
        sel_el_type.show();
        sel_note_type.hide();
        sel_task_type.hide();
        inp_textar1.show();
        inp_date1.hide();
        inp_phone1.hide();
        inp_num1.attr("placeholder", 'введите id');
        break;

      case '3':
        sel_el_type.show();
        sel_note_type.show();
        sel_task_type.hide();
        inp_date1.hide();
        if (sel_note_type.find(":selected").val() === '4') {
            inp_textar1.show();
            inp_phone1.hide();
        } else {
            inp_textar1.hide();
            inp_phone1.show();
        }
        inp_num1.attr("placeholder", 'введите id');
        break;

      case '4':
        sel_el_type.show();
        sel_note_type.hide();
        sel_task_type.show();
        inp_textar1.show();
        inp_date1.show();
        inp_phone1.hide();
        inp_num1.attr("placeholder", 'введите id');
        break;

      case '5':
        sel_el_type.hide();
        sel_note_type.hide();
        sel_task_type.hide();
        inp_textar1.show();
        inp_date1.hide();
        inp_phone1.hide();
        inp_num1.attr("placeholder", 'введите id');
        break;

      default:
        sel_el_type.hide();
        sel_note_type.hide();
        sel_task_type.hide();
        inp_textar1.hide();
        inp_date1.hide();
        inp_phone1.hide();
        inp_num1.attr("placeholder", 'количество элементов');
    }
  });
  inp_but1.on("click", function () {
    var ajdata;
    base_type = sel_base.find(":selected").val();
    switch (base_type) {
      case '1':
        if (inp_num1.val() <= 0 || inp_num1.val() > 10000) {
          alert('значение должно быть положительным и меньше 10000');
          return false;
        }
        ajdata = {
          num: inp_num1.val()
        };
        req_url = 'control.php/task_1';
        break;

      case '2':
        if (!inp_num1.val() || !inp_textar1.val()) {
          alert('заполните все поля');
          return false;
        }
        ajdata = {
          id: inp_num1.val(),
          elem_type: sel_el_type.find(":selected").val(),
          text: inp_textar1.val()
        };
        req_url = 'control.php/task_2';
        break;

      case '3':
        ajdata = {
          id: inp_num1.val(),
          elem_type: sel_el_type.find(":selected").val(),
          note_type: sel_note_type.find(":selected").val()
        };
        req_url = 'control.php/task_3';
        if (sel_note_type.find(":selected").val() === '4') {
          if ( !inp_num1.val() || !inp_textar1.val()) {
            alert('заполните все поля');
            return false;
          }
          ajdata.text = inp_textar1.val();
        } else {
          if ( !inp_num1.val() || !inp_phone1.val()) {
            alert('заполните все поля');
            return false;
          }
          ajdata.text = inp_phone1.val();
        }
        break;

      case '4':
        if (!inp_num1.val() || !inp_textar1.val() || !inp_date1.val()) {
          alert('заполните все поля');
          return false;
        }
        req_url = 'control.php/task_4';
        let cur_time = (Date.parse(inp_date1.val())/1000);
        ajdata = {
          id: inp_num1.val(),
          elem_type: sel_el_type.find(":selected").val(),
          text: inp_textar1.val(),
          task_type: sel_task_type.find(":selected").val(),
          date: cur_time
        };
        break;

      case '5':
        if (!inp_num1.val()) {
          alert('заполните все поля');
          return false;
        }
        req_url = 'control.php/task_5';
        ajdata = {
          id: inp_num1.val(),
          text: inp_textar1.val()
        };
        break;
        
      default:
        return false;
    }
    $.ajax({
      type: 'POST',
      data: ajdata,
      url: req_url,
      beforeSend: function () {
        inp_but1.attr('disabled', true);
      },
      success: function (msg) {
        alert(msg);
      },
      complete: function () {
        inp_but1.attr('disabled', false);
      }
    });
  });
});
