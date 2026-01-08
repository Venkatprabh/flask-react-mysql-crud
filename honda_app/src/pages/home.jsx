import React from 'react';
import { Card, Row, Col } from 'react-bootstrap';

function Home() {
  return (
    <div>
      <h1 className="mb-4">Welcome to FullStack Application</h1>
      <p className="lead mb-5">
        This is a complete full-stack application built with React.js, Python Flask, and SQLite.
      </p>
      
      <Row>
        <Col md={4}>
          <Card className="mb-4">
            <Card.Body>
              <Card.Title>React.js Frontend</Card.Title>
              <Card.Text>
                Modern, responsive user interface built with React.js and Bootstrap
              </Card.Text>
            </Card.Body>
          </Card>
        </Col>
        <Col md={4}>
          <Card className="mb-4">
            <Card.Body>
              <Card.Title>Python Backend</Card.Title>
              <Card.Text>
                RESTful API built with Flask and SQLAlchemy for database operations
              </Card.Text>
            </Card.Body>
          </Card>
        </Col>
        <Col md={4}>
          <Card className="mb-4">
            <Card.Body>
              <Card.Title>SQL Database</Card.Title>
              <Card.Text>
                SQLite database with proper models and relationships using SQLAlchemy ORM
              </Card.Text>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </div>
  );
}

export default Home;