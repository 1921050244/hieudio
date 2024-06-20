<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Khung danh sách thành viên -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                       Hộp thư đến
                    </div>
                    <div class="card-body">
                        <ul class="list-group member-list"></ul>
                    </div>
                </div>
            </div>
            <!-- Khung hiển thị tin nhắn -->
            <div class="col-md-8">
                <div class="card d-none" id="chat-card">
                    <div class="card-header" id="receiver-name">
                        <!-- Tên người nhận sẽ được cập nhật bằng JavaScript -->
                    </div>
                    <div class="card-body message-box">
                        <div class="message-box">
                            <!-- Tin nhắn sẽ được chèn ở đây -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Form nhập tin nhắn -->
                        <form id="chat-form">
                            <div class="form-group">
                                <textarea id="message" class="form-control" rows="3" placeholder="Nhập tin nhắn..."></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Gửi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS và jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Script JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            getMembers();
        });

        var receiverId;

        function getMembers() {
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var members = JSON.parse(xhr.responseText);
                    displayMembers(members);
                }
            };

            xhr.open("GET", "modules/quanlythongbao/get_members.php", true);
            xhr.send();
        }

        function displayMembers(data) {
            var memberList = document.querySelector(".list-group.member-list");
            memberList.innerHTML = "";

            try {
                var members = data.members;

                members.forEach(function(member) {
                    var listItem = document.createElement("li");
                    listItem.textContent = member.email;

                    var totalUnread = member.total_unread;

                    var unreadCount = document.createElement("span");
                    unreadCount.textContent = totalUnread; 
                    unreadCount.classList.add("unread-count");

                    listItem.setAttribute("data-user-id", member.id_user);
                    listItem.classList.add("list-group-item");
                    listItem.addEventListener("click", function() {
                        receiverId = member.id_user;
                        displayChat(receiverId);
                        // Hiển thị phần chat khi nhấn vào thành viên
                        document.getElementById("chat-card").classList.remove("d-none");
                    });

                    listItem.appendChild(unreadCount);
                    memberList.appendChild(listItem);
                });
            } catch (error) {
                console.error("Error parsing JSON response:", error.message);
            }
        }

        function displayChat(userId) {
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var messages = JSON.parse(xhr.responseText);
                    var messageBox = document.querySelector(".message-box");
                    messageBox.innerHTML = "";

                    messages.forEach(function(message) {
                        var messageItem = document.createElement("div");
                        messageItem.textContent = message.noidung;
                        messageItem.classList.add("message");

                        if (message.id_nguoigui == userId) {
                            messageItem.classList.add("received");
                        } else {
                            messageItem.classList.add("sent");
                        }

                        messageBox.insertAdjacentElement("beforeend", messageItem);
                    });

                    messageBox.scrollTop = messageBox.scrollHeight;

                    if (messageBox.scrollHeight > messageBox.clientHeight) {
                        messageBox.classList.add("scrollable");
                    } else {
                        messageBox.classList.remove("scrollable");
                    }
                }
            };

            xhr.open("GET", "modules/quanlythongbao/get_messages.php?userId=" + userId, true);
            xhr.send();

            var memberList = document.querySelector(".list-group.member-list");
            var receiverName = memberList.querySelector('[data-user-id="' + userId + '"]').textContent;
            document.getElementById("receiver-name").textContent = receiverName;
        }

        document.addEventListener("DOMContentLoaded", function() {
            var form = document.getElementById("chat-form");
            var messageInput = document.getElementById("message");

            form.addEventListener("submit", function(event) {
                event.preventDefault();
                var message = messageInput.value.trim();

                if (message !== "") {
                    if (receiverId) {
                        sendMessage(receiverId, message);
                    } else {
                        alert("Vui lòng chọn một người nhận trước khi gửi tin nhắn.");
                    }
                    messageInput.value = "";
                }
            });
        });

        function sendMessage(receiverId, message) {
            var messageInput = document.getElementById("message");

            // Tạo dữ liệu gửi đi
            var data = new URLSearchParams();
            data.append("receiverId", receiverId);
            data.append("message", message);

            console.log("Sending message to receiverId:", receiverId);  // Debug log
            console.log("Message content:", message);  // Debug log

            // Sử dụng Fetch API để gửi yêu cầu POST
            fetch("modules/quanlythongbao/send_message.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Cache-Control": "no-cache, no-store, must-revalidate", // Đảm bảo trình duyệt không lưu cache
                    "Pragma": "no-cache",
                    "Expires": "0"
                },
                body: data
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Có lỗi xảy ra khi gửi tin nhắn.');
                }
                return response.text(); // hoặc response.json() nếu server trả về dữ liệu JSON
            })
            .then(text => {
                console.log("Response text:", text); // Xử lý thành công
                displayChat(receiverId);
                messageInput.value = ""; // Xóa nội dung input sau khi gửi thành công
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        }
    </script>
</body>
</html>
