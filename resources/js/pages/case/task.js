$(document).on('click', '.updateTaskListBtn', function (e) {
    const index = $(e.currentTarget).closest('tr').data('index');
    $('#taskListSend').attr('data-index', index).data('index', index)

    let tasks = $(`tbody > tr[data-index=${index}] > td:nth-child(3) > ul > li`).map(function () {
        return $(this).text()
    }).get()
    $('input:checkbox[name=taskList]').prop('checked', false);
    $('#taskListForm label').map(function (){
        let list = $(this);
        tasks.forEach((item) => {
            if (list.text() === item){
                list.prevAll('input').prop('checked', true)
            }
        })
    })
})

$(document).on('click', '#taskListSend', function () {
    let checkItem = [];
    $("input:checkbox[name=taskList]:checked").each(function () {
        checkItem.push($(this).nextAll('label').text());
    });
    const index = $('#taskListSend').data('index')
    if (checkItem.length) {
        let ul = document.createElement('ul');
        checkItem.forEach((item) => {
            let li = document.createElement("li");
            li.innerText = item;
            ul.appendChild(li);
        })
        ul.classList.add('mb-0')
        $(`tr[data-index=${index}] > td:nth-child(3)`).html(ul)
    }else{
        $(`tr[data-index=${index}] > td:nth-child(3)`).html(null)
    }
    $('#taskListModal').modal('hide')
})

$('#updateCaseTaskSend').on('click', function () {
    const taskList = $('tr li').map(function () {
        week = $(this).closest('tr').data('index') + 1; // data-index start as 0
        return {
            'week': week,
            'content': $(this).text()
        }
    }).get();

    var form = document.createElement('form');
    form.setAttribute('action', task_post);
    form.setAttribute('method', 'POST');

    var input0 = document.createElement('input');
    input0.setAttribute('type', 'hidden');
    input0.setAttribute('name', '_token');
    input0.setAttribute('value', csrf_token);


    var input2 = document.createElement('input');
    input2.setAttribute('type', 'text');
    input2.setAttribute('name', 'taskList');
    input2.setAttribute('value', JSON.stringify(taskList));

    var submit = document.createElement('input');
    submit.setAttribute('type', 'submit');

    form.appendChild(input0);
    form.appendChild(input2);
    form.appendChild(submit);

    document.body.appendChild(form).submit();
})