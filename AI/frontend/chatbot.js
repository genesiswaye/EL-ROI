async function sendMessage(){

    let input =
    document.getElementById("message");

    let text =
    input.value;

    if(!text){

        return;

    }

    addMessage(
        text,
        "user-message"
    );

    input.value = "";


    let response = await fetch(

        "http://127.0.0.1:8000/chat",

        {

            method:"POST",

            headers:{

                "Content-Type":
                "application/json"

            },

            body:JSON.stringify({

                user_id:1,
                message:text

            })

        }

    );

    let data =
    await response.json();

    addMessage(

        data.reply,
        "bot-message"

    );

}


function addMessage(
    text,
    className
){

    let div =
    document.createElement("div");

    div.className =
    className;

    div.innerHTML =
    text.replace(/\n/g,"<br>");

    document
    .getElementById(
        "chat-messages"
    )
    .appendChild(div);

}


function sendSuggestion(
    text
){

    document
    .getElementById(
        "message"
    )
    .value = text;

    sendMessage();

}