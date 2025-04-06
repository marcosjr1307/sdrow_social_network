$(document).ready(function(){
    $('.formLogin').on('submit', function(event){
        event.preventDefault();
        $.ajax({
            url: '/api2/login',
            data: $(this).serialize(),
            dataType: 'json',
            method: 'post',
            success: function(data){
                if (data.status){
                    $('.textError').text('').addClass("text-hidden");
                    window.location.href = '/';
                }else{
                    $('.textError').text(data.msg).removeClass("text-hidden");
                }
            }
        });
    });
});

$(document).ready(function(){
    $('.formCadastro').on('submit', function(event){
        event.preventDefault();
        $.ajax({
            url: '/api2/cadastro',
            data: $(this).serialize(),
            dataType: 'json',
            method: 'post',
            success: function(data){
                if (data.status){
                    $('.textError').text("").addClass('text-hidden');
                    window.location.href = '/';
                }else{
                    if(data.msg == 2){ //As senhas são diferentes
                        $('.textError').text('As senhas informadas são diferentes!').removeClass('text-hidden');
                    }else if(data.msg == 3){ //Email já cadastrado
                        $('.textError').text('O email informado já está cadastrado!').removeClass('text-hidden');
                    }else if(data.msg == 4){ //Telefone já cadastrado
                        $('.textError').text('O telefone informado já está cadastrado!').removeClass('text-hidden');
                    }else if(data.msg == 5){ //Usuário já existe
                        $('.textError').text('O Nome de usuário não está disponível!').removeClass('text-hidden');
                    }
                }
            }
        });
    });
});