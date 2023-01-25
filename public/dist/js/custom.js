function noti(data){
    if (data.success) {
        Swal.fire({
            title:'Success',
            html:data.success,
            icon:'success',
            timer: 2000,
            timerProgressBar: true,
        });
    }
    if (data.error) {
        errText = '';
        data.error.forEach(err => {
            errText+=err+'<br>';
        });
        Swal.fire({
            title:'Error',
            html:errText,
            icon:'error',
        })
    }
}
