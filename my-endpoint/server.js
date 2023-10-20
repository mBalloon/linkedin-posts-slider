// server.js
const express = require('express');
const myFunction = require('./myFunction');
const app = express();
const port = 3000;

app.get('/my-endpoint', (req, res) => {
    myFunction();
    res.send('Function has been run');
});

app.listen(port, () => {
    console.log(`Server is running at http://localhost:${port}`);
});