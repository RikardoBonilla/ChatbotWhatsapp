<!DOCTYPE html>
<html>
<head>
    <title>WhatsApp Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 10px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>WhatsApp Chatbot Test</h1>

    <form id="whatsappForm">
        <div class="form-group">
            <label for="phone">Phone Number (Colombian format):</label>
            <input type="text" id="phone" placeholder="+573001234567" value="+573001234567">
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" rows="3" placeholder="Enter your test message">Hello from WhatsApp Chatbot!</textarea>
        </div>

        <button type="submit">Send WhatsApp Message</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('whatsappForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            const resultDiv = document.getElementById('result');

            try {
                const response = await fetch('/api/whatsapp/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone_number: phone,
                        content: message
                    })
                });

                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <strong>✅ Success!</strong><br>
                            Message ID: ${data.message_id}<br>
                            Status: ${data.message}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <strong>❌ Error!</strong><br>
                            ${data.error}
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="result error">
                        <strong>❌ Network Error!</strong><br>
                        ${error.message}
                    </div>
                `;
            }
        });
    </script>
</body>
</html>