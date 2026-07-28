import os

pages = [
    "index", "about", "register", "login", "dashboard", 
    "events", "profile", "contact", "admin", "faq", "feedback"
]

page_titles = {
    "index": "Home",
    "about": "About Us",
    "register": "Register",
    "login": "Login",
    "dashboard": "Dashboard",
    "events": "Events",
    "profile": "User Profile",
    "contact": "Contact Us",
    "admin": "Admin Panel",
    "faq": "FAQ",
    "feedback": "Feedback"
}

base_dir = r"e:\WEBDEV\Web-Dev-Repo\StudentHub"
os.makedirs(base_dir, exist_ok=True)
os.makedirs(os.path.join(base_dir, "css"), exist_ok=True)
os.makedirs(os.path.join(base_dir, "images"), exist_ok=True)

nav_links = "\n            ".join([f'<a href="{p}.html">{page_titles[p]}</a> | ' for p in pages])

template = """<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - {title}</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

    <header>
        <h1>Welcome to StudentHub!</h1>
        <h2>Your portal for all student needs</h2>
    </header>

    <nav aria-label="Main Navigation">
        <p>
            {nav_links}
        </p>
    </nav>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="Breadcrumb">
        <p>You are here: <a href="index.html">Home</a> {breadcrumb_current}</p>
    </nav>

    <main>
        <section>
            <h3>{title} Page</h3>
            <article>
                <p>Hello! This is the {title} page of our project.</p>
                <p>We are learning semantic HTML and accessibility.</p>
                {extra_content}
            </article>
        </section>
        
        <aside>
            <h4>Important Links</h4>
            <ul>
                <li><a href="events.html">Check Events</a></li>
                <li><a href="faq.html">Read FAQ</a></li>
            </ul>
        </aside>
    </main>

    <footer>
        <p>Copyright 2026 - My Web Dev Project</p>
        <nav aria-label="Footer Navigation">
            <a href="about.html">About</a> | 
            <a href="contact.html">Contact Us</a> | 
            <a href="feedback.html">Give Feedback</a>
        </nav>
    </footer>

</body>
</html>
"""

def get_extra_content(page):
    if page == "login":
        return """
        <form action="dashboard.html" method="GET" aria-label="Login Form">
            <label for="uname">Username:</label><br>
            <input type="text" id="uname" name="uname" required aria-required="true"><br><br>
            
            <label for="pwd">Password:</label><br>
            <input type="password" id="pwd" name="pwd" required aria-required="true"><br><br>
            
            <input type="submit" value="Log In">
        </form>
        """
    elif page == "register":
        return """
        <form action="login.html" method="GET" aria-label="Registration Form">
            <label for="fname">First Name:</label><br>
            <input type="text" id="fname" name="fname" required aria-required="true"><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required aria-required="true"><br><br>
            
            <label for="newpwd">Choose Password:</label><br>
            <input type="password" id="newpwd" name="newpwd" required aria-required="true"><br><br>
            
            <input type="submit" value="Sign Up">
        </form>
        """
    elif page == "contact":
        return """
        <form action="index.html" method="GET" aria-label="Contact Form">
            <label for="name">Your Name:</label><br>
            <input type="text" id="name" name="name" required aria-required="true"><br><br>
            
            <label for="msg">Message:</label><br>
            <textarea id="msg" name="msg" rows="4" cols="30" required aria-required="true"></textarea><br><br>
            
            <input type="submit" value="Submit">
        </form>
        """
    return ""

for p in pages:
    title = page_titles[p]
    breadcrumb = f'&gt; <strong>{title}</strong>' if p != 'index' else ''
    content = template.format(
        title=title, 
        nav_links=nav_links, 
        breadcrumb_current=breadcrumb,
        extra_content=get_extra_content(p)
    )
    filepath = os.path.join(base_dir, f"{p}.html")
    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)

print("Beginner Pages generated successfully.")
