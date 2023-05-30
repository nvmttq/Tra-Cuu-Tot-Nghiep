var socket = io('http://localhost:3000');

socket.on('push-to-check', function(data){
  var table = document.getElementById('data-table');
  var newRow = table.insertRow();
  var studentIdCell = newRow.insertCell();
  // var nameCell = newRow.insertCell();
  // var degreeCell = newRow.insertCell();
  // var majourCell = newRow.insertCell();

  studentIdCell.innerHTML = data['studentId'];
  // nameCell.innerHTML = data['name'];
  // degreeCell.innerHTML = data['degree'];
  // majourCell.innerHTML = data['majour'];
});

$(document).ready(function(){
  $('#btnSubmit').on('click', function(){
    let studentId = document.getElementById('txtStudentId').innerText;
    socket.emit('push-to-check', studentId);
  });
});