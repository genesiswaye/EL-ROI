from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from chatbot.chatbot_handler import process_chat
from database import (

    get_history

)

app = FastAPI()

app.add_middleware(

    CORSMiddleware,

    allow_origins=["*"],

    allow_credentials=True,

    allow_methods=["*"],

    allow_headers=["*"]

)

@app.post("/chat")

@app.post("/chat")

async def chat(data:dict):

    user_id=data.get(
        "user_id"
    )

    session_id=data.get(
        "session_id"
    )

    message=data.get(
        "message"
    )

    response=await process_chat(

        user_id,
        message

    )

    return{

        "reply":response

    }

# @app.get("/chat-history/{user_id}")

# async def history(

#     user_id:int

#     ):

#     sessions= get_chat_sessions(user_id)

#     return sessions

# @app.get("/chat-session/{session_id}")

# async def get_session(

#     session_id:int

#     ):

#     messages= get_session_messages(session_id)

#     return messages

# @app.post(

# "/new-chat"

# )

# async def new_chat(

#     data:dict

#     ):

#     user_id= data.get("user_id")

#     session_id= create_chat_session(user_id, "New Chat")

#     return{

#     "session_id":

#     session_id

#     }

@app.get(

"/chat-memory/{user_id}"

)

async def memory(

    user_id:int

    ):

    return get_history(

    user_id

    )