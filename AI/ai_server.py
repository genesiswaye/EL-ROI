from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from chatbot.chatbot_handler import process_chat

app = FastAPI()

app.add_middleware(

    CORSMiddleware,

    allow_origins=["*"],

    allow_credentials=True,

    allow_methods=["*"],

    allow_headers=["*"]

)

@app.post("/chat")

async def chat(data: dict):

    user_id = data.get("user_id")
    message = data.get("message")

    response = await process_chat(
        user_id,
        message
    )

    return {

        "reply": response

    }