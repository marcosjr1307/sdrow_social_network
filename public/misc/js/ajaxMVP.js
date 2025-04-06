$(document).ready(function(){
    $('.card-post-img').each(function(index, elem){
        var img = $(elem).data('img');
        if (img) {
            $(elem).css('background-image', 'url(' + img + ')');
        }
    });
    $(document).on('click', '.item-like', doLike);
    $(document).on('dblclick', '.card-post-img', doLike);
    $(document).on('click','.btnFollowUser', followUser)
    $(document).on('click', '.optionPosts', getPosts);
    $(document).on('click', '.optionCurtidos', getCurtidos);
    $(document).on('click', '.btnLogout', doLogout);
    $(document).on('click', '.search-btn-main', goPerfil)

    function goPerfil(e){
        e.preventDefault();
        var search_username = document.querySelector(".search-txt-main");
        $.ajax({
            success: function(data){
                window.location.href = '/perfil/'+ search_username.value;
            }
        });
    }

    function doLogout(e){
        e.preventDefault();
        $.ajax({
            url: '../../logout',
            method: 'POST',
            dataType: 'json',
            success: function(data){
                if(data.status){
                    window.location.href = '/';
                }else{
                    console.error("Erro ao fazer logout")
                }
                
            }
        });
    }

    function getPosts(e){
        e.preventDefault();
        $username = $(this).data("username");
        $.ajax({
            success: function (data){
                window.location.href = '/perfil/'+$username;
            }
        })
    }

    function getCurtidos(e){
        e.preventDefault();
        $username = $(this).data("username");
        $.ajax({
            success: function (data){
                window.location.href = '/curtidos/'+$username;
            }
        })
    }
    
    function followUser(e){
        e.preventDefault();
        var btnSeguir = $('.btnFollowUser');
        var followers = $('.followers');
        $.ajax({
            url: '/api/followUser',
            dataType: 'json',
            method: 'POST',
            data: 'username='+$(this).data("username"),
            success: function(data){
                if(data.status){
                    switch(data.msg){
                        case 'followed':
                            $(btnSeguir).text("Deixar de seguir");
                            $(followers).text(data.qtdFollowers+ " seguidores");
                            break;
                        case 'unfollowed':
                            $(btnSeguir).text("Seguir");
                            $(followers).text(data.qtdFollowers+ " seguidores");
                            break;
                    }
                }
            }
        });
    }


    function doLike(){
        var id = $(this).data('idpost');
        var likeBtn = $('.item-like[data-idpost='+ id +']');
        var likeBtn1 = likeBtn.children('i');
        var qtdCurtidas = $('.curtidas[data-idpost='+ id +']');
        $.ajax({
            url: '/api/like',
            dataType: 'json',
            data: { id_post: id },
            method: 'POST',
            success: function(data){
                switch (data.status){
                    case 'likeSuccess':
                        $(likeBtn1).removeClass('material-symbols-outlined');
                        $(likeBtn1).addClass('material-icons item-like-liked');
                        $(qtdCurtidas).text(data.qtdLikes+" curtidas");
                        break;
                    case 'deslikeSuccess':
                        $(likeBtn1).removeClass('material-icons item-like-liked');
                        $(likeBtn1).addClass('material-symbols-outlined');
                        $(qtdCurtidas).text(data.qtdLikes+" curtidas");
                        break;
                }
            }
        });
    }
});
