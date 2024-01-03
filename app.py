from PIL import Image
from flask import Flask, render_template, request, jsonify
from waitress import serve
import tesserocr
import re
import logging
import signal
import os
import sys
import time


app = Flask(__name__)

app.logger.setLevel(logging.DEBUG)

# Restart server
def restart_server(signum, frame):
    print("Restarting server...")
    os.execv(sys.executable, [sys.executable] + sys.argv)

if __name__ == '__main__':
    serve(app, host='0.0.0.0', port=5000)

    signal.signal(signal.SIGHUP, restart_server)

    app.run(debug=True)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api/data', methods=['GET'])
def get_data():
    data = [
        {"name": "Pessoa", "document": "839.107.090-52", "birthdate": "03/08/1999"},
    ]
    return jsonify(data)

@app.route('/process_image', methods=['POST'])

def process_image():
    app.logger.info('Processing image...')
    start_time = time.time()

    if 'image' not in request.files:
        app.logger.error('No image provided')
        return jsonify({'error': 'No image provided'})

    uploaded_image = request.files['image']
    image_path = 'images/image1.png'
    uploaded_image.save(image_path)

    try:
        with tesserocr.PyTessBaseAPI(lang='por') as api:
            api.SetImageFile(image_path)
            extracted_text = api.GetUTF8Text()
    except Exception as e:
        app.logger.error(f"Error during OCR: {e}")
        return jsonify({'error': 'Error during OCR'})

    app.logger.info(f"Extracted Text: {extracted_text}")

    name = extract_name(extracted_text)
    app.logger.info(f"Extracted Name: {name}")

    birthdate = extract_birthdate(extracted_text)
    app.logger.info(f"Extracted Birthdate: {birthdate}")

    document = extract_document(extracted_text)
    app.logger.info(f"Extracted Document: {document}")

    end_time = time.time()
    processing_time = end_time - start_time
    app.logger.info(f"Processing time: {processing_time} seconds")

    return jsonify({'document': document, 'name': name, 'birthdate': birthdate})

def extract_name(text):
    name_match = re.search(r'\n(.+?)\s+\d{3}\.\d{3}\.\d{3}-\d{2}', text)
    if name_match:
        return name_match.group(1).strip()
    return None

def extract_birthdate(text):
    birthdate_match = re.search(r'\d{3}\.\d{3}\.\d{3}-\d{2}\s+(\d{2}/\d{2}/\d{4})', text)
    if birthdate_match:
        return birthdate_match.group(1)
    return None

def extract_document(text):
    cpf_match = re.search(r'(\d{3}\.\d{3}\.\d{3}-\d{2})', text)
    if cpf_match:
        return cpf_match.group(1)
    return None

if __name__ == '__main__':
    serve(app, host='0.0.0.0', port=5000)
