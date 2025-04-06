var btnSalvar = document.querySelector(".btn-salvar");
var btn_altera_foto = document.querySelector(".btn-altera-foto");
var inputImg = document.querySelector(".inputImg");
var previewImg = document.querySelector(".previewImg");
var msg_error = document.querySelector(".msg-error");
var msg_error_img = document.querySelector(".msg-error-img");

btn_altera_foto.addEventListener("click", function(){
    msg_error.innerHTML = "";
    msg_error_img.innerHTML = "";
    inputImg.click();
})

inputImg.addEventListener('change', function(event){
    var file = event.target.files[0];
    if(file){
        var lastImg = previewImg.src;
        const reader = new FileReader();
        reader.onload = function(e){
            previewImg.src = e.target.result;
        }
        reader.readAsDataURL(file);

        Swal.fire({
            title: "Você tem certeza?",
            text: "Sua imagem será alterada!",
            icon: "warning",
            width: 400,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, alterar!",
            background: "#4145C4",
            color: "#fff"
          }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('image', file); 
                        fetch('uploadIMG', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.status){
                                window.location.reload();
                            }else if(!data.status){
                                if(data.message == 'Nenhum dado recebido via POST'){
                                    msg_error_img.innerHTML = "Selecione uma imagem!";
                                    previewImg.src = lastImg;
                                }else if(data.message == 'Selecione a imagem novamente'){
                                    msg_error_img.innerHTML = "Selecione uma imagem!";
                                    previewImg.src = lastImg;
                                }else if(data.message == 'Arquivo muito grande'){
                                    msg_error_img.innerHTML = "Arquivo muito grande!";
                                    previewImg.src = lastImg;
                                }else if(data.message == 'Tipo de arquivo não aceito'){
                                    msg_error_img.innerHTML = "Formato de arquivo não aceito";
                                    previewImg.src = lastImg;
                                }else if(data.message == "Erro ao mover o arquivo para o diretório"){
                                    msg_error_img.innerHTML = "Erro";
                                    previewImg.src = lastImg;
                                }else if(data.message == "Erro ao salvar no banco de dados"){
                                    msg_error_img.innerHTML = "Erro no servidor";
                                    previewImg.src = lastImg;
                                }
                            }
                        })
                        .catch(error=>{
                            console.error(error);
                        })
                        inputImg.value = "";
                    }else{
                        inputImg.value = "";
                        previewImg.src = lastImg;
                    }
          });
    }
});

btnSalvar.addEventListener("click", function(){
    msg_error.innerHTML = "";
    var usernameBD = this.dataset.username; 
    var biografiaBD = this.dataset.biografia;
    var txtUsername = document.querySelector(".input-username").value; 
    var txtBiografia = document.querySelector(".input-biografia").value; 
    Swal.fire({
        title: "Você tem certeza?",
        text: "Seus dados serão alterados!",
        icon: "warning",
        width: 400,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, alterar!",
        background: "#4145C4",
        color: "#fff"
      }).then((result) => {
                if (result.isConfirmed) {
                    if(usernameBD == txtUsername){
                        if(txtBiografia != biografiaBD){
                            $.ajax({
                                url: '/api/alterUserDataBiografia',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    biografiaChanged: txtBiografia
                                },
                                success: function(data){
                                    if(data.status){
                                        window.location.reload();
                                    }else{
                                        msg_error.innerHTML = "Erro ao alterar dados!";
                                        console.error(data.msg);
                                    }
                                }
                            });
                        }else{
                            window.location.reload();
                        }
                       
                    }else{
                        $.ajax({
                            url: '/api/alterAllUserData',
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                usernameChanged: txtUsername,
                                biografiaChanged: txtBiografia
                            },
                            success: function(data){
                                if(data.status){
                                    console.log('entrou');
                                    $.ajax({
                                        url: 'changedData',
                                        method: 'POST',
                                        dataType: 'json',
                                        data: {
                                            username: txtUsername
                                        }
                                    });
                                    window.location.reload();
                                }else{
                                    if(data.msg == "Erro ao alterar dados"){
                                        msg_error.innerHTML = "Erro no servidor!";
                                    }else if(data.msg == "Username indisponível!"){
                                        msg_error.innerHTML = "Username indisponível";
                                    }else if(data.msg == "Nenhum dado recebido via POST"){
                                        msg_error.innerHTML = "Erro ao enviar dados!";
                                    }
                                }
                            }
                        });
                    }
                }else{
                    msg_error.innerHTML = "";
                }
      });

});


