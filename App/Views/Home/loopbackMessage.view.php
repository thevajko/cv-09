<script>
    const message = {author: 'Patrik', recipient: 'Peter', message: 'Ahoj'}

    window.onload = () => {
        document.getElementById('sendJson').onclick = () => {
            fetch('http://localhost/?c=Home&a=loopbackMessage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(message)
            }).then(response => response.json()).then(data => {
                document.getElementById('result').innerHTML = `Autor: ${data.author} <br>Recipient: ${data.recipient} <br>Message: ${data.message}`
            }).catch((error) => {
                document.getElementById('result').innerText = 'Error: ' + error
            })
        }
    }
</script>

<button id="sendJson">Poslať správu</button>
<br><br>
<div id="result"></div>