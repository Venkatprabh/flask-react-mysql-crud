from flask import Flask, jsonify, request
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from dotenv import load_dotenv
import os
from sqlalchemy.exc import IntegrityError

# Load environment variables
load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for React frontend

# Database configuration
# app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv('DATABASE_URL', 'sqlite:///app.db')
app.config['SQLALCHEMY_DATABASE_URI'] = ("mysql+pymysql://root:@localhost/honda_db")
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'dev-secret-key')

db = SQLAlchemy(app)

# # Database Models
# class User(db.Model):
#     id = db.Column(db.Integer, primary_key=True)
#     name = db.Column(db.String(80), unique=True, nullable=False)
#     email = db.Column(db.String(120), unique=True, nullable=False)
    
#     def to_dict(self):
#         return {
#             'id': self.id,
#             'name': self.name,
#             'email': self.email
#         }

# # # Create tables
# with app.app_context():
#     db.create_all()

# # API Routes
# @app.route('/api/users', methods=['GET'])
# def get_users():
#     try:
#         users = User.query.all()
#         return jsonify([user.to_dict() for user in users])
#     except Exception as e:
#         print("GET USERS ERROR:", e)
#         return jsonify({"error": str(e)}), 500
    

# @app.route('/api/users', methods=['POST'])
# def create_user():
#     data = request.json
#     try:
#         user = User(name=data['name'], email=data['email'])
#         db.session.add(user)
#         db.session.commit()
#         return jsonify(user.to_dict()), 201
#     except IntegrityError:
#         db.session.rollback()
#         return jsonify({"error": "Email already exists"}), 400



# @app.route('/api/users/<int:id>', methods=['PUT'])
# def update_user(id):
#     user = User.query.get_or_404(id)
#     data = request.json
#     user.name = data.get('name', user.name)
#     user.email = data.get('email', user.email)
#     db.session.commit()
#     return jsonify(user.to_dict())

# @app.route('/api/users/<int:id>', methods=['DELETE'])
# def delete_user(id):
#     user = User.query.get_or_404(id)
#     db.session.delete(user)
#     db.session.commit()
#     return jsonify({'message': 'User deleted'})

# if __name__ == '__main__':
#     app.run(debug=True, port=5000)

# ----------------------------------------------------------------------------------------------------------

# in the below code I've changed "User and user" into "Bike and bike".
# Database Models
class Bike(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(80), unique=True, nullable=False)
    price = db.Column(db.Integer, nullable=False)
    
    def to_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'price': self.price
        }

# # Create tables
with app.app_context():
    db.create_all()

# API Routes
@app.route('/api/bike', methods=['GET'])
def get_bikes():
    try:
        bikes = Bike.query.all()
        return jsonify([bike.to_dict() for bike in bikes])
    except Exception as e:
        print("GET BIKES ERROR:", e)
        return jsonify({"error": str(e)}), 500
    

@app.route('/api/bike', methods=['POST'])
def create_bike():
    data = request.json
    try:
        bike = Bike(name=data['name'], price=data['price'])
        db.session.add(bike)
        db.session.commit()
        return jsonify(bike.to_dict()), 201
    except IntegrityError:
        db.session.rollback()
        return jsonify({"error": "name already exists"}), 400



@app.route('/api/bike/<int:id>', methods=['PUT'])
def update_bike(id):
    bike = Bike.query.get_or_404(id)
    data = request.json
    bike.name = data.get('name', bike.name)
    bike.price = data.get('email', bike.price)
    db.session.commit()
    return jsonify(bike.to_dict())

@app.route('/api/bike/<int:id>', methods=['DELETE'])
def delete_bike(id):
    bike = Bike.query.get_or_404(id)
    db.session.delete(bike)
    db.session.commit()
    return jsonify({'message': 'bike deleted'})

if __name__ == '__main__':
    app.run(debug=True, port=5000)