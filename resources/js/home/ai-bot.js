async function askGemini(prompt) {
  const response = await fetch("/api/gemini", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ prompt }),
  });

  const data = await response.json();
  return data.candidates?.[0]?.content?.parts?.[0]?.text || "I couldn't generate a reply.";
}

async function sendMessage() {
  const input = document.querySelector(".chat-window input");
  const chatBox = document.querySelector(".chat-window .chat");
  const userMessage = input.value.trim();

  if (userMessage.length) {
    input.value = "";
    chatBox.insertAdjacentHTML("beforeend", `
      <div class="user">
        <p>${userMessage}</p>
      </div>
    `);

    // Auto-scroll after user message
    chatBox.scrollTop = chatBox.scrollHeight;

    // Show loading indicator
    chatBox.insertAdjacentHTML("beforeend", `
      <div class="ai loading">
        <p>Thinking...</p>
      </div>
    `);

    // Auto-scroll after loading indicator
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
      const aiReply = await askGemini(userMessage);

      // Remove loading indicator
      const loadingElem = chatBox.querySelector(".ai.loading");
      if (loadingElem) loadingElem.remove();

      chatBox.insertAdjacentHTML("beforeend", `
        <div class="ai">
          <p>${aiReply}</p>
        </div>
      `);

      // Auto-scroll after AI reply
      chatBox.scrollTop = chatBox.scrollHeight;

    } catch (err) {
      const loadingElem = chatBox.querySelector(".ai.loading");
      if (loadingElem) loadingElem.remove();

      chatBox.insertAdjacentHTML("beforeend", `
        <div class="ai error">
          <p>Sorry, something went wrong.</p>
        </div>
      `);

      // Auto-scroll after error message
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  }
}

function openChatWithPrompt(prompt) {
    // Show chat window
    document.querySelector(".chat-window").classList.remove("ai-bot-hidden");
    document.getElementById("ai-bot-toggle").style.display = "none";
    
    // Set the input value
    const input = document.querySelector(".chat-window input");
    input.value = prompt;
    
    // Automatically send the message
    sendMessage();
    
    // Scroll chat to bottom
    const chatBox = document.querySelector(".chat-window .chat");
    setTimeout(() => {
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 100);
}

// Make it globally accessible so volcano-modal.js can use it
window.openChatWithPrompt = openChatWithPrompt;

document.addEventListener("DOMContentLoaded", function() {
    // Show chat window
    document.getElementById("ai-bot-toggle").addEventListener("click", function() {
        document.querySelector(".chat-window").classList.remove("ai-bot-hidden");
        this.style.display = "none";
    });

    // Hide chat window
    document.getElementById("ai-bot-close").addEventListener("click", function() {
        document.querySelector(".chat-window").classList.add("ai-bot-hidden");
        document.getElementById("ai-bot-toggle").style.display = "block";
    });

    // Send message (if you have this)
    document.querySelector(".chat-window .input-area button").addEventListener("click", sendMessage);

    // Send message on Enter key press
    document.querySelector(".chat-window .input-area input").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            sendMessage();
        }
    });
});