import pymysql


def get_connection():

    return pymysql.connect(

        host="localhost",

        user="root",

        password="",

        database="finalyearproject_db",

        cursorclass=pymysql.cursors.DictCursor

    )


def get_wallet_balance(user_id):

    conn = get_connection()

    try:

        with conn.cursor() as cursor:

            sql = """

            SELECT balance

            FROM wallets

            WHERE user_id=%s

            """

            cursor.execute(

                sql,

                (user_id,)

            )

            result = cursor.fetchone()

            return (

                result["balance"]

                if result

                else 0

            )

    finally:

        conn.close()



def get_completed_jobs(user_id):

    conn = get_connection()

    try:

        with conn.cursor() as cursor:

            sql = """

            SELECT COUNT(*) total

            FROM jobs

            WHERE freelancer_id=%s

            AND status='completed'

            """

            cursor.execute(

                sql,

                (user_id,)

            )

            result = cursor.fetchone()

            return result["total"]

    finally:

        conn.close()



def get_pending_withdrawals(user_id):

    conn = get_connection()

    try:

        with conn.cursor() as cursor:

            sql = """

            SELECT

            SUM(amount) total

            FROM withdrawals

            WHERE user_id=%s

            AND status='pending'

            """

            cursor.execute(

                sql,

                (user_id,)

            )

            result = cursor.fetchone()

            return result["total"] or 0

    finally:

        conn.close()


# def create_chat_session(
#     user_id,
#     first_message
# ):

#     conn = get_connection()

#     try:

#         with conn.cursor() as cursor:

#             sql = """

#             INSERT INTO chatbot_sessions

#             (

#             user_id,
#             title

#             )

#             VALUES(

#             %s,
#             %s

#             )

#             """

#             title = first_message[:40]

#             cursor.execute(

#                 sql,

#                 (

#                 user_id,
#                 title

#                 )

#             )

#             conn.commit()

#             return cursor.lastrowid

#     finally:

#         conn.close()


# def save_chat_message(

#     session_id,
#     role,
#     message

# ):

#     conn = get_connection()

#     try:

#         with conn.cursor() as cursor:

#             sql = """

#             INSERT INTO chatbot_messages(

#             session_id,
#             role,
#             message

#             )

#             VALUES(

#             %s,
#             %s,
#             %s

#             )

#             """

#             cursor.execute(

#                 sql,

#                 (

#                 session_id,
#                 role,
#                 message

#                 )

#             )

#             conn.commit()

#     finally:

#         conn.close()


# def get_chat_sessions(

#     user_id

# ):

#     conn = get_connection()

#     try:

#         with conn.cursor() as cursor:

#             sql = """

#             SELECT *

#             FROM chatbot_sessions

#             WHERE user_id=%s

#             ORDER BY created_at DESC

#             LIMIT 10

#             """

#             cursor.execute(

#                 sql,

#                 (user_id,)

#             )

#             return cursor.fetchall()

#     finally:

#         conn.close()


# def get_session_messages(

#     session_id

# ):

#     conn = get_connection()

#     try:

#         with conn.cursor() as cursor:

#             sql = """

#             SELECT

#             role,
#             message,
#             created_at

#             FROM chatbot_messages

#             WHERE session_id=%s

#             ORDER BY created_at ASC

#             """

#             cursor.execute(

#                 sql,

#                 (session_id,)

#             )

#             return cursor.fetchall()

#     finally:

#         conn.close()

def get_session_history(
session_id
):

    conn=get_connection()

    with conn.cursor() as cursor:

        sql="""

        SELECT

        role,
        message content

        FROM chatbot_messages

        WHERE session_id=%s

        ORDER BY id ASC

        """

        cursor.execute(

        sql,

        (session_id,)

        )

        return cursor.fetchall()
    
def save_message(

user_id,
role,
message

):

    conn=get_connection()

    with conn.cursor() as cursor:

        sql="""

        INSERT INTO
        chatbot_messages(

        user_id,
        role,
        message

        )

        VALUES(

        %s,
        %s,
        %s

        )

        """

        cursor.execute(

        sql,

        (

        user_id,
        role,
        message

        )

        )

        conn.commit()

def get_history(

user_id

):

    conn=get_connection()

    with conn.cursor() as cursor:

        sql="""

        SELECT

        role,
        message, 
        created_at

        FROM chatbot_messages

        WHERE user_id=%s

        ORDER BY id DESC

        LIMIT 10

        """

        cursor.execute(

    sql,

    (user_id,)

)

    messages = list(cursor.fetchall())
    messages.reverse()
   

    return messages