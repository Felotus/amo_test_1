$(document).ready(function(){
	let sel_el_type = $('#sel2');
    let sel_base = $('#sel1');
    let sel_note_type = $('#sel3');
    let sel_task_type = $('#sel4');
    let inp_textar1 = $('#textar1');
    let inp_date1 = $('#date1');
    let inp_phone1 = $('#phone1');
    let inp_num1 = $('#num1');
    let inp_but1 = $('#but1');
	$('#sel1 option:eq(1)').prop('selected', true);
  	sel_base.prop('selected', true);
    sel_el_type.hide();
    sel_note_type.hide();
    sel_task_type.hide();
    inp_textar1.hide();
    inp_date1.hide();
    inp_phone1.hide();
	$.ajax({
		type: "POST",
		url: "task_types_get.php",
		success: function(msg){
			msg = JSON.parse(msg);
			$.each(msg, function(key, value){

                sel_task_type.append($('<option>', {
					value: key,
					text: value,
				}));
			})
		}
	});
    sel_note_type.change(function() {
		if(sel_note_type.find(":selected").val() === '4'){
            inp_textar1.show();
            inp_phone1.hide();
		}
		else{
            inp_textar1.hide();
            inp_phone1.show();
		}
	});
    sel_base.change(function() {
		switch (sel_base.find(":selected").val()) {
			case '1':
                sel_el_type.hide();
                sel_note_type.hide();
                sel_task_type.hide();
                inp_textar1.hide();
                inp_date1.hide();
                inp_phone1.hide();
                inp_num1.attr("placeholder", "количество элементов");
				break;
			case '2':
                sel_el_type.show();
                sel_note_type.hide();
                sel_task_type.hide();
                inp_textar1.show();
                inp_date1.hide();
                inp_phone1.hide();
                inp_num1.attr("placeholder", "введите id");
				break;
			case '3':
                sel_el_type.show();
                sel_note_type.show();
                sel_task_type.hide();
                inp_date1.hide();
				if(sel_note_type.find(":selected").val() === '4'){
                    inp_textar1.show();
                    inp_phone1.hide();
				}
				else{
                    inp_textar1.hide();
                    inp_phone1.show();
				}
                inp_num1.attr("placeholder", "введите id");
				break;
			case '4':
                sel_el_type.show();
                sel_note_type.hide();
                sel_task_type.show();
                inp_textar1.show();
                inp_date1.show();
                inp_phone1.hide();
                inp_num1.attr("placeholder", "введите id");
				break;
			case '5':
                sel_el_type.hide();
                sel_note_type.hide();
                sel_task_type.hide();
                inp_textar1.show();
                inp_date1.hide();
                inp_phone1.hide();
                inp_num1.attr("placeholder", "введите id");
				break;
			default:
                sel_el_type.hide();
                sel_note_type.hide();
                sel_task_type.hide();
                inp_textar1.hide();
                inp_date1.hide();
                inp_phone1.hide();
                inp_num1.attr("placeholder", "введите id");
				break;
        }
    });

    inp_but1.on("click", function(){
		let link;
		let ajdata;
		switch (sel_base.find(":selected").val()) {
			case '1':
				if (inp_num1.val() <= 0 || inp_num1.val() > 10000){
					alert('значение должно быть положительным и меньше 10000');
					return false;
                }
                link = "amo_mass_create.php";
				ajdata = {
					num : inp_num1.val()
				};
				break;
			case '2':
				if (!inp_num1.val() || !inp_textar1.val()) {
					alert('заполните все поля');
					return false;
                }
                link = "textfield_create.php";
				ajdata = {
					id : inp_num1.val(),
					elem_type : sel_el_type.find(":selected").val(),
					text : inp_textar1.val()
				};
				break;
			case '3':
				link = "event_add.php";
				ajdata = {
					id : inp_num1.val(),
					elem_type : sel_el_type.find(":selected").val(),
					note_type : sel_note_type.find(":selected").val()
				};
				if (sel_note_type.find(":selected").val() === '4') {
					if ( !inp_num1.val() || !inp_textar1.val()) {
						alert('заполните все поля');
					return false;
                    }
                    ajdata.text = inp_textar1.val();
				} else {
					if( !inp_num1.val() || !inp_phone1.val()) {
						alert('заполните все поля');
					return false;
                    }
                    ajdata.text = inp_phone1.val();
				}
				break;
			case '4':
				if ( !inp_num1.val() || !inp_textar1.val() || !inp_date1.val()) {
					alert('заполните все поля');
					return false;
                }
                link = "task_add.php";
				let cur_time = (Date.parse(inp_date1.val())/1000);
				ajdata = {
					id : inp_num1.val(),
					elem_type : sel_el_type.find(":selected").val(),
					text : inp_textar1.val(),
					task_type : sel_task_type.find(":selected").val(),
					date : cur_time
				};
				break;
			case '5':
				if (!inp_num1.val()) {
					alert('заполните все поля');
					return false;
                }
                link = "task_close.php";
				ajdata = {
					id : inp_num1.val(),
					text : inp_textar1.val()
				};
				break;
			default:
				return false;
        }
        $.ajax({
			type: "POST",
			data: ajdata,
			url: link,
			beforeSend: function(){
				inp_but1.attr('disabled', true);
			},
			success: function(msg){
				alert(msg);
			},
			complete: function(){
				inp_but1.attr('disabled', false);
			}
		});
	});
});


//amo_mass_create.php
//textfield_create.php
//event_add.php
//task_types_get.php
//task_add.php
//task_close.php