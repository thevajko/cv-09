<script>
    const data = {author: 'Patrik', recipient: 'Peter', message: 'Ahoj'}

    window.onload = () => {
        document.getElementById('sendJson').onclick = () => {
            fetch('http://localhost/?c=Home&a=receiveJson', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(response => response.json()).then(data => {
                document.getElementById('result').innerHTML = `Autor: ${data.author} <br>Recipient: ${data.recipient} <br>Message: ${data.message}`
            }).catch((error) => {
                document.getElementById('result').innerText = 'Error: ' + error
            })
        }
    }
</script>

<button id="sendJson">Poslať JSON</button>
<br><br>
<div id="result"></div>
