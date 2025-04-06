var idPostComment = "";
$(document).ready(function(){
    $(document).on('click', '.btnComments', openPost);
    var modal_comments = document.querySelector(".dialog_comments");
    var btn_close = document.querySelector(".close_comments");
    var imagem_post = document.querySelector(".imagem-post");
    var imagem_user_post = document.querySelector(".imagem-user-post");
    var username_post = document.querySelector(".comments-top-username");
    var description_post = document.querySelector(".post-description");
    var text_area_comment = document.querySelector(".textarea-comment");
    var charCountComments = document.querySelector(".comment_char_count");

    text_area_comment.addEventListener("keydown", function(event){
        if(event.key === "Enter"){
            event.preventDefault();
        }
    });

    text_area_comment.addEventListener('input', function(){
        const length = 235 - text_area_comment.value.length;
        charCountComments.textContent = `${length}`;

        if(length > 30){
            charCountComments.style.color = 'white';
        } else if(length >= 1 && length <= 30){
            charCountComments.style.color = 'yellow';
        } else if(length === 0){
            charCountComments.style.color = 'red';
        }
    });

    modal_comments.addEventListener('close', function(e){
        resetModal();
    });

    function openPost(e){
        e.preventDefault();
        $id = $(this).data("idpost");
        var id = $id;
        idPostComment = id;
        //console.log(id);
        $.ajax({
            url: '/api/loadPost',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id
            },
            success: function(data){
                //console.log(data);
                if(data.status){
                    data.dadosPost.forEach(function(post) {
                        imagem_post.src = post.imagem;
                        if(post.foto != null){
                            imagem_user_post.src = post.foto;
                        }else{
                            imagem_user_post.src = "../misc/imagens/semFoto.png";
                        }
                        username_post.innerHTML = post.username_usuario;
                        description_post.innerHTML = post.descricao;
                    });
                    if(data.dadosComentarios != 0){
                        data.dadosComentarios.forEach(function(comment){
                            if(comment.foto != null){
                                var commentHTML = `
                                <div class="comment">
                                    <div class="comment-box-img">
                                        <img src="${comment.foto}" alt="Foto do usuário"> 
                                    </div>
                                    <p class="comment-text">
                                        <span class="comment-username">${comment.username_usuario}</span>
                                        <span class="comment-text-span">${comment.mensagem}</span>
                                    </p>
                                </div>
                                `;
                            }else{
                                var commentHTML = `
                                <div class="comment">
                                    <div class="comment-box-img">
                                        <img src="../misc/imagens/semFoto.png" alt="Foto do usuário"> 
                                    </div>
                                    <p class="comment-text">
                                        <span class="comment-username">${comment.username_usuario}</span>
                                        <span class="comment-text-span">${comment.mensagem}</span>
                                    </p>
                                </div>
                                `;
                            }
                            
                            $(".box-comments").append(commentHTML);
                        });
                    }
                }else{
                    console.error("Erro na busca!");
                    console.error(data);
                }
            }
        });
        document.body.classList.add("no-scroll");
        modal_comments.showModal();
    }

    btn_close.addEventListener("click", function(e){
        resetModal();
        idPostComment = null;
        modal_comments.close();
    }); 

    function resetModal(){
        text_area_comment = document.querySelector(".textarea-comment");
        text_area_comment.value = "";
        imagem_post.src = '../upload/posts/default.png';
        imagem_user_post.src = '../upload/posts/default.png';
        username_post.innerHTML = "";
        description_post.innerHTML = "";
        $(".box-comments").empty();
        document.body.classList.remove("no-scroll");

    }

    var btnSendComment = document.querySelector(".sendComment");
    btnSendComment.addEventListener("click", function(){
        $.ajax({
            url: '/api/sendComment',
            method: 'POST',
            dataType:'json',
            data: {
                idpost: idPostComment,
                message: text_area_comment.value,
                username: this.dataset.username
            },
            success: function(data){
                if(data.status){
                    window.location.reload();
                    
                }else{ 
                    console.error("Erro ao comentar");
                }
            }
        });
    });

});

function showOptions(button){
    var username_post = $(button).data("username");
    var box_username = document.querySelector(".user-username");
    var username = box_username.dataset.username; 
    var idpost = button.dataset.idpost;
    var optionsBox = document.querySelector(".post"+idpost);
    var deletePost = document.querySelector('.delete'+idpost);
    
    if(username_post == username){
        deletePost.textContent = "Excluir Publicação!";
        deletePost.style.display = "Block";
    }else{
        deletePost.style.display = "none";
    }

    var allOptionsBoxes = document.querySelectorAll(".options-box");
    allOptionsBoxes.forEach(box => {
        if (box !== optionsBox) {
            box.style.display = "none";
        }
    }); 

    if(optionsBox.style.display == "" || optionsBox.style.display == "none"){
        optionsBox.style.display = "block"; 
    }else if(optionsBox.style.display == "block"){
        optionsBox.style.display = "none";
    }
    
    window.onclick = function(event) {
        if (!button.contains(event.target) && !optionsBox.contains(event.target)) {
            optionsBox.style.display = "none";
        }
    };
}

function copiarDesc(element){
    var description = element.dataset.desc;
    navigator.clipboard.writeText(description);
    var optionsBox = element.closest('.options-box');
    optionsBox.style.display = "none";
}   

function deletePost(element){
    Swal.fire({
        title: "Você tem certeza?",
        text: "Você nao consigará recuperar essa publicação posteriormente!",
        icon: "warning",
        width: 400,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, exluir!",
        background: "#4145C4",
        color: "#fff"
      }).then((result) => {
        if (result.isConfirmed) {
            var id = element.dataset.id;
            console.log(id);
            $.ajax({
                url: '/api/deletePost',
                method: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data){
                    if(data.status){
                        window.location.reload();
                    }else{
                        console.error("Erro na exclusão!");
                    }
                }
            });
        }
      });
}



