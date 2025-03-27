(function($){
    $.PagSeguro = function(){

    };

    $.PagSeguro.prototype = {
        endForm: '',
        Real: function(valor){
            return Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(valor);
        },
        Iniciar: function(){

            var th = this;

            // Inicializar a classe do PagSeguro para encriptar o cartão de crédito.
            PagSeguro.setUp({
                env: window.pagseguro.env,
                session: window.pagseguro.session
            });

            $(document).ready(function(){

                // Detactar input do cartão para pegar as parcelas.
                /*$("#numCartao").on('blur', function(e){
                    var num = $(this).val().substr(0, 6);
                    if(num.length >= 6){
                        $.blockUI({message: 'Carregando parcelas...'});
                        th.Parcelas(num, window.valorPlano);
                    } else {
                        //$("#qntParcelas").html("");
                        $.unblockUI();
                    }
                });*/

                // Enviar forma de pagamento.
                $("#formPagamento").on('submit', function(e){
                    e.preventDefault();
                    th.Pagamento();
                });
                
                // Remover local de login caso já esteja logado.
                if(window.logado === 'S'){
                    $(".hideLogin").remove();
                    $(".hideConta").remove();
                    $(".hideComprador").remove();
                    $(".email").remove();
                }


            })

           
            

        },
        Parcelas: function(bin, valor){
            $.post(`${window.url}/parcelas`, {
                bin: bin,
                valor: valor,
            }).then((resp) => {
                var parcelasResp = JSON.parse(resp);
                let options = '';
                for(let cartao in parcelasResp.payment_methods.credit_card){
                    for(let parcela of parcelasResp.payment_methods.credit_card[cartao].installment_plans){
                        options += `<option value='${parcela.installments}'>${parcela.installments}x de ${this.Real(parcela.installment_value)} (${this.Real(parcela.amount.value)})</option>`;
                    }
                }

                $("#qntParcelas").html(options);
                $.unblockUI();
            }).catch((ex) => {
                $("#qntParcelas").html("");
                $.unblockUI();
            });

        },
        Metodo: function(){
            var metodo = $("input[name='paymentMethod']:checked").val();
            if(metodo !== 'creditCard'){
                $("#creditCardHolderBirthDate").attr('disabled', true);
                $("#creditCardHolderCPF").attr('disabled', true);
                $("#cvvCartao").attr("disabled", true);
                $("#anoValidade").attr('disabled', true);
                $("#mesValidade").attr('disabled', true);
                $("#creditCardHolderName").attr('disabled', true);
               // $("#qntParcelas").attr('disabled', true);
                $("#numCartao").attr('disabled', true);
                $(".creditCard").slideUp('fast');

            } else {
                $("#creditCardHolderBirthDate").removeAttr('disabled');
                $("#creditCardHolderCPF").removeAttr('disabled');
                $("#cvvCartao").removeAttr('disabled');
                $("#anoValidade").removeAttr('disabled');
                $("#mesValidade").removeAttr('disabled');
                $("#creditCardHolderName").removeAttr('disabled');
                $("#numCartao").removeAttr('disabled');
                //$("#qntParcelas").removeAttr('disabled');
                $(".creditCard").slideDown('fast');
            }
        },
        Pagamento: function(){
            $.blockUI({message: `
                <div class="row"><div class="col-md-12">Processando pedido...<br>
                <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div></div></div>
            `});
            var th = this;

            var metodo = $("input[name='paymentMethod']:checked").val();

            
            // Se for cartão de crédito criptografa o mesmo.
            if(metodo === 'creditCard'){

                var holder = $("#creditCardHolderName").val();
                var number = $("#numCartao").val();
                var expMonth = $("#mesValidade").val();
                var expYear = $("#anoValidade").val();
                var securityCode = $("#cvvCartao").val();

                // Criptografar Cartão.
                var encrypted = PagSeguro.encryptCard({
                    publicKey: window.pagseguro.public_key,
                    holder,
                    number,
                    expMonth,
                    expYear,
                    securityCode,
                });

                if(encrypted?.hasErrors === true){
                    $.unblockUI();
                    const erros = encrypted?.errors.map(error => th.erroEncryptCard(error.code)).join('</br>');
                    Swal.fire({
                        title: 'Falha na validação do cartão',
                        html: erros,
                        icon: 'error',
                        confirmButtonColor: 'red'
                    });

                    return;

                }

                $("#encryptedCard").val(encrypted.encryptedCard);
                $("#cardBin").val(number.substr(0, 6));

            }

            var formData = $("#formPagamento").serialize();

            // Enviar form
            $.ajax({
                type: "POST",
                url: `${window.url}/assinatura/pagamento`,
                data: formData,
                dataType: 'json',
                success: function(resps){
                    $.unblockUI();
                    let resp = JSON.parse(resps);
                    if(resp?.status === false){
                        Swal.fire({title: 'Falha', html: resp?.msg ?? 'No foi possível processar o seu pedido. Tente novamente.', icon: 'error', confirmButtonColor: 'red'}).then((reply) => {
                            if(resp?.reload === true){
                                window.location.reload();
                                return;
                            }
                        })
                        return;
                    }

                    

                    if(resp?.pix){
                        Swal.fire({
                            title: 'Faça o Pagamento PIX',
                            imageUrl: resp.data.qr_code,
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'PIX',
                            allowOutsideClick: false,
                            html: `
                            Valor do PIX: <b>${th.Real(window.valorPlano)}</b>
                            <br>
                            PIX Copia e Cola:
                            <br>
                            Expiração: <b>${resp.data.expiracao}</b>
                            <br>
                            <code style="font-size: 10px;" id='copia'>${resp.data.copia_cola}</code>

                            <br>
                            <span class='text-info'>Após o pagamento, sua assinatura estara ativa automaticamente.</span>
                            <script>
                                $("#copia").on('click', function(e){
                                    var $temp = $('<input>');
                                    $temp.val($("#copia").text()).select();
                                    document.execCommand('copy');
                                    $temp.remove();
                                })
                                
                            </script>
                            `,
                            confirmButtonColor: 'green',
                        }).then((rp) => window.location.href = window.url);

                        return;
                    }

                    if(resp?.boleto){
                        Swal.fire({
                            title: 'Pagamento por Boleto',
                            icon: 'info',
                            html: `Efetue o pagamento de <b>${th.Real(window.valorPlano)}</b> através do código de barras:<br>
                            <code>${resp.data.cod_barra}</code><br>
                            Ou se preferir clique no botão imprimir para imprimir o boleto.
                            <br>
                            Data de expiração do boleto: <b>${resp.data.expiracao}</b>
                            `,
                            confirmButtonColor: 'green',
                            showCancelButton: true,
                            cancelButtonColor: 'blue',
                            cancelButtonText: 'Imprimir Boleto'
                        }).then((res) => {
                            if(res.isDismissed){
                                window.open(resp.data.link, "_blank");
                                return window.location.href = window.url;
                            }
                            return window.location.href = window.url;
                        })
                        return;
                    }

                    if(resp?.cartao){
                        Swal.fire({
                            title: 'Confirmado',
                            icon: 'success',
                            html: 'Seu pagamento foi confirmado com sucesso. Em alguns instantes sua assinatura estara ativa.',
                            confirmButtonColor: 'green',
                        }).then((re) => window.location.href = window.url);
                        return;
                    }

                },
                error: function(err){
                    $.unblockUI();
                    Swal.fire({title: 'Falha', html: 'Não foi possível processar seu pagamento no momento. Tente novamente mais tarde.', icon: 'error', confirmButtonColor: 'red'});
                    return;
                }
            })


        },
        erroEncryptCard(erro){
            switch(erro){
              case 'INVALID_NUMBER': return 'Número do cartão inválido.';
              case 'INVALID_SECURITY_CODE': return 'Código de segurança do cartão inválido. Você deve informar um valor de 3, 4 ou mais digitos.';
              case 'INVALID_EXPIRATION_MONTH': return 'Mês de validade inválido. Você deve informar entre 1 em 12.';
              case 'INVALID_EXPIRATION_YEAR': return 'Ano de validade invlido. Você deve informar 4 digitos.';
              case 'INVALID_PUBLIC_KEY': return 'Chave públic inválida!';
              case 'INVALID_HOLDER': return 'Títular do cartão inválido!';
              default: return 'Erro desconhecido, se o problema persistir. Contate o suporte.';
            }
        },
        TipoConta: function(){
            var tipoConta = $("input[name='tipo_conta']:checked").val();

            if(tipoConta === 'existente'){
                $("#senha_repetir").attr('disabled', true);
                $(".senhaRepetir").slideUp('fast');
                this.endForm = $(".hideConta").html();
                this.compForm = $(".hideComprador").html();
                $(".hideComprador").html('');
                $(".hideConta").html("");
            } else {
                $("#senha_repetir").removeAttr('disabled');
                $(".senhaRepetir").slideDown('fast');
                $(".hideComprador").html(this.compForm);
                $(".hideConta").html(this.endForm);
                
            }
        }
    }


}(jQuery));

var pagseguroClass = new $.PagSeguro();

$(document).ready(function(){
    pagseguroClass.Iniciar();
    pagseguroClass.Metodo();
});