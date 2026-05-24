conversation_history = {}


def get_history(user_id):

    if user_id not in conversation_history:

        conversation_history[user_id] = []

    return conversation_history[user_id]


def save_message(
    user_id,
    role,
    content
):

    if user_id not in conversation_history:

        conversation_history[user_id] = []

    conversation_history[user_id].append(

        {
            "role": role,
            "content": content
        }

    )

    conversation_history[user_id] = conversation_history[user_id][-10:]