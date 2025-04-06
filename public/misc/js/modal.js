var btnPublicacao = document.querySelector(".btnPublicar");
var btnPublicacaoPopup = document.querySelector(".btnPublicarPopup");
var modal = document.querySelector('.dialog');
var btnClose = document.querySelector('.close');
var box = document.querySelector('.dialog-box-mid');

var inputFile = document.querySelector('#picture_input');
var pictureImagem = document.querySelector('.picture_image');
var picture = document.querySelector('.picture');
var pictureImagemTxt = 'Escolha uma imagem';
pictureImagem.innerHTML = pictureImagemTxt;
var imgExists = false;
const form = document.getElementById("form-post");
const error_img_msg = document.querySelector(".error_img_msg");
const textarea = document.querySelector('.description');
const charCount = document.querySelector('.char-count');

document.addEventListener('DOMContentLoaded', function(event) {
    textarea.addEventListener("keydown", function(event){
        if(event.key === "Enter"){
            event.preventDefault();
        }
    });

    textarea.addEventListener('input', function() {
        const length = 235 - textarea.value.length;
        charCount.textContent = `${length}`;
        
        if(length > 30){
            charCount.style.color = 'white';
        } else if(length >= 1 && length <= 30){
            charCount.style.color = 'yellow';
        } else if(length === 0){
            charCount.style.color = 'red';
        }
    });

    

    form.addEventListener("submit", function(event){
        event.preventDefault();

        const formData = new FormData(form);

        fetch("/", {
            method: "POST",
            body: formData
        })
        
        .then(response => response.text())
        .then(result => {
            if (result.includes("Imagem muito grande")){
                error_img_msg.textContent = 'Imagem muito grande! Máximo: 2MB';
            }else if(result.includes("Tipo de arquivo não aceito")) {
                error_img_msg.textContent = 'Tipo de arquivo não aceito!';
            }else {
                var description = '';
                var path = '';
                var teste = result;
                if(result.includes("path=")){
                    path = result.split('path=')[1].split('/0')[0];
                }
                path = "/" + path;
                description = textarea.value;
                $.ajax({
                    url: '/api/sendPost',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        path: path,
                        description: description
                    },
                    success: function(data){
                        if(data.status){
                            window.location.href = '/';
                        }else{
                            console.error("Erro ao enviar post");
                        }
                    }
                });
                error_img_msg.textContent = '';
                modal.close();
            }
        })
        .catch(error => {
            console.error("Erro ao enviar o formulário:", error);
        });

    });
});

modal.addEventListener('close', function(event){
    event.preventDefault();
    resetModal();
});

inputFile.addEventListener("click", function(e){
    imgExists = false;
    if(inputFile.files[0]){
        imgExists = true;
    }
});

inputFile.addEventListener('change', function(e){
    var inputTarget = e.target;
    var file = inputTarget.files[0];

    if(file){
        var reader = new FileReader();

        reader.addEventListener('load', function(e){
            var readerTarget = e.target;
            
            var img_src = readerTarget.result;
            console.log(img_src);

            pictureImagem.innerHTML = '';
            box.style.backgroundImage = 'url(' + img_src + ')';
            picture.style.border = 'none';
            error_img_msg.innerHTML = "";
        });

        reader.readAsDataURL(file);
    }else{
        if(!imgExists){
            pictureImagem.innerHTML = pictureImagemTxt;
        } 
    }
});


btnPublicacao.addEventListener('click', function (event){
    event.preventDefault();
    modal.showModal();
    if(!imgExists){
        pictureImagem.innerHTML = pictureImagemTxt;
    } 
});

btnPublicacaoPopup.addEventListener('click', function (event){
    event.preventDefault();
    modal.showModal();

    if(!imgExists){
        pictureImagem.innerHTML = pictureImagemTxt;
    } 
});

btnClose.addEventListener('click', function(event){
    event.preventDefault();
    resetModal();
    modal.close();
})

function resetModal(){
    pictureImagem.innerHTML = "";
    box.style.backgroundImage = '';
    inputFile.value = '';
    imgExists = false;
    textarea.value = '';
}

