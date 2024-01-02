from flask import Flask, render_template, jsonify
from waitress import serve

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api/data', methods=['GET'])
def get_data():
    data = [
        {"name": "Nome da Pessoa", "document": "839.107.090-52", "birthdate": "03/08/1999"},
    ]
    return jsonify(data)

if __name__ == '__main__':
    serve(app, host='0.0.0.0', port=5000)