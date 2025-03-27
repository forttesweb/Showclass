var conn;
function openSocket(token){
    conn = new WebSocket('wss://showclass.com.br:8085?token='+token);
    console.log(conn)

    conn.onopen = function (event) {
        console.log('Connection Established');
    };
}