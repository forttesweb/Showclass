var identificadorDeConta = "Client_Id_296b41368515dfab4c7b795a64012acc808b304f";

var s = document.createElement('script');
s.type = 'text/javascript';
var v = parseInt(Math.random() * 1000000);
s.src = 'https://sandbox.gerencianet.com.br/v1/cdn/' + identificadorDeConta + '/' + v;
s.async = false;
s.id = identificadorDeConta;
if (!document.getElementById(identificadorDeConta)) {
    document.getElementsByTagName('head')[0].appendChild(s);
};
$gn = {
    validForm: true,
    processed: false,
    done: {},
    ready: function (fn) {
        $gn.done = fn;
    }
};