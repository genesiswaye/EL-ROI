import pymysql


connection = pymysql.connect(

    host="localhost",
    user="root",
    password="",
    database="finalyearproject_db",
    cursorclass=pymysql.cursors.DictCursor

)


def get_wallet_balance(user_id):

    with connection.cursor() as cursor:

        sql = """

        SELECT balance
        FROM wallets
        WHERE user_id = %s

        """

        cursor.execute(sql, (user_id,))

        result = cursor.fetchone()

        if result:

            return result["balance"]

        return 0


def get_completed_jobs(user_id):

    with connection.cursor() as cursor:

        sql = """

        SELECT COUNT(*) AS total
        FROM jobs
        WHERE freelancer_id = %s
        AND status = 'completed'

        """

        cursor.execute(sql, (user_id,))

        result = cursor.fetchone()

        return result["total"]


def get_pending_withdrawals(user_id):

    with connection.cursor() as cursor:

        sql = """

        SELECT SUM(amount) AS total
        FROM withdrawals
        WHERE user_id = %s
        AND status = 'pending'

        """

        cursor.execute(sql, (user_id,))

        result = cursor.fetchone()

        if result["total"]:

            return result["total"]

        return 0