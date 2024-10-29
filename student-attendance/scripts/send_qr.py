import pandas as pd
import qrcode
import os
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase
from email import encoders
from email_validator import validate_email, EmailNotValidError


# Function to generate a QR code for each URN
def generate_qr_code(urn, file_name):
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr.add_data(urn)
    qr.make(fit=True)
    img = qr.make_image(fill="black", back_color="white")
    img.save(file_name)


# Function to send an email with the QR code attached
def send_email(recipient_email, subject, body, qr_code_filename):
    # Replace these with your email provider's settings
    smtp_server = "smtp.gmail.com"  # e.g., Gmail SMTP
    smtp_port = 587
    sender_email = "devesh97531@gmail.com"  # Replace with your email
    sender_password = (
        "uewj vpjd cljg fxde"  # Replace with your email password or app password
    )

    # Create the email
    msg = MIMEMultipart()
    msg["From"] = sender_email
    msg["To"] = recipient_email
    msg["Subject"] = subject

    # Attach the body of the email
    msg.attach(MIMEText(body, "plain"))

    # Attach the QR code image
    attachment = open(qr_code_filename, "rb")
    part = MIMEBase("application", "octet-stream")
    part.set_payload(attachment.read())
    encoders.encode_base64(part)
    part.add_header("Content-Disposition", f"attachment; filename= {qr_code_filename}")
    msg.attach(part)

    # Connect to the email server and send the email
    try:
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()  # Secure the connection
        server.login(sender_email, sender_password)
        text = msg.as_string()
        server.sendmail(sender_email, recipient_email, text)
        server.quit()
        print(f"Email sent successfully to {recipient_email}")
    except Exception as e:
        print(f"Failed to send email to {recipient_email}: {str(e)}")


# Load the CSV file
df = pd.read_csv("students.csv")  # Your CSV file with columns: name, urn, email

# Iterate over each row in the CSV
for index, row in df.iterrows():
    name = row["name"]
    urn = row["urn"]
    email = row["email"]

    # Validate email address
    try:
        valid = validate_email(email)  # Validate email address
        email = valid.email
    except EmailNotValidError as e:
        print(f"Invalid email address {email}: {str(e)}")
        continue

    # Generate the QR code for the current URN
    qr_code_filename = f"qrcode_{urn}.png"
    generate_qr_code(urn, qr_code_filename)

    # Prepare the email content
    subject = "Your QR Code"
    body = f"Hello {name},\n\nHere is your URN QR code."

    # Send the email
    send_email(email, subject, body, qr_code_filename)

    # Remove the QR code file after sending
    os.remove(qr_code_filename)
