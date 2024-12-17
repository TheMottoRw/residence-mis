from flask import Flask, request, jsonify
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

app = Flask(__name__)

# Replace these with your email details
SMTP_SERVER = 'smtp.gmail.com'
SMTP_PORT = 587
SENDER_EMAIL = 'itguyrw@gmail.com'  # Your email address
SENDER_PASSWORD = 'epyrtxnbfgglddtu'  # Your email password

def send_email(subject, recipient, body):
    try:
        # Set up the server and login
        server = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)
        server.starttls()  # Secure the connection
        server.login(SENDER_EMAIL, SENDER_PASSWORD)

        # Create the message
        msg = MIMEMultipart()
        msg['From'] = SENDER_EMAIL
        msg['To'] = recipient
        msg['Subject'] = subject

        # Attach the body text to the message
        msg.attach(MIMEText(body, 'html'))

        # Send the email
        server.sendmail(SENDER_EMAIL, recipient, msg.as_string())
        server.quit()

        return jsonify({'status':True,'message': 'Email sent successfully!'}), 200
    except Exception as e:
        return jsonify({'status':False,'message': f'Error: {str(e)}'}), 500

@app.route('/sendemail', methods=['POST'])
def send_email_api():
    # Get the data from the POST request
    data = request.get_json()
    print(data)

    # Check if the required fields are in the request data
    if 'subject' not in data or 'to' not in data or 'email' not in data:
        return jsonify({status:False,'message': 'Missing required fields'}), 400

    subject = data['subject']
    to_email = data['to']
    email_body = data['email']

    # Send the email
    return send_email(subject, to_email, email_body)

if __name__ == '__main__':
    app.run(debug=True)
