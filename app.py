from flask import Flask, render_template, jsonify
from waitress import serve

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api/data', methods=['GET'])
def get_data():
    data = [
        {"id": 1, "name": "John Doe", "email": "john@example.com"},
        {"id": 2, "name": "Jane Doe", "email": "jane@example.com"}
    ]
    return jsonify(data)

if __name__ == '__main__':
    serve(app, host='0.0.0.0', port=5000)