window.onpageshow = function () {
    if (loading){
        loading.remove();
    }
}

$('#loginSend').click(function () {
    $('body').prepend(loading);

    // // const formData = new FormData(document.querySelector('#loginForm'))
    // const account = $('#account').val();
    // const password = $('#password').val();
    // const formData = new FormData();
    // formData.append('account', account);
    // formData.append('password', password);
    // axios({
    //     url: url,
    //     method: 'POST',
    //     data: formData
    // }).then(function (res) {
    //     console.log(res)
    // }).catch(function (err) {
    //     console.log(err)
    // }).finally(function () {
    //     console.log('finally')
    // })
})