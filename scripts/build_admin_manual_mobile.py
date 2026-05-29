"""
Builds the Golden Pangolin admin manual — MOBILE EDITION — as a styled PDF.
Run: python scripts/build_admin_manual_mobile.py
Output: docs/Golden_Pangolin_Admin_Manual_Mobile.pdf
"""
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

GOLD      = colors.HexColor("#d4a03c")
GOLD_DARK = colors.HexColor("#b8872e")
INK       = colors.HexColor("#1a1208")
INK_SOFT  = colors.HexColor("#3a3a4a")
MUTED     = colors.HexColor("#6b7280")
CREAM     = colors.HexColor("#fdf5e6")
BORDER    = colors.HexColor("#e5e7eb")

ROOT = Path(__file__).resolve().parent.parent
OUT  = ROOT / "docs" / "Golden_Pangolin_Admin_Manual_Mobile.pdf"
OUT.parent.mkdir(parents=True, exist_ok=True)

ss = getSampleStyleSheet()

title_style = ParagraphStyle(
    "GpTitle", parent=ss["Title"],
    fontName="Helvetica-Bold", fontSize=26, leading=32,
    textColor=INK, spaceAfter=10, alignment=1,
)
subtitle_style = ParagraphStyle(
    "GpSubtitle", parent=ss["Normal"],
    fontName="Helvetica", fontSize=12, leading=18,
    textColor=GOLD_DARK, spaceAfter=24, alignment=1,
)
h1 = ParagraphStyle(
    "GpH1", parent=ss["Heading1"],
    fontName="Helvetica-Bold", fontSize=18, leading=24,
    textColor=INK, spaceBefore=18, spaceAfter=10, keepWithNext=True,
)
h2 = ParagraphStyle(
    "GpH2", parent=ss["Heading2"],
    fontName="Helvetica-Bold", fontSize=13, leading=18,
    textColor=GOLD_DARK, spaceBefore=14, spaceAfter=6, keepWithNext=True,
)
h3 = ParagraphStyle(
    "GpH3", parent=ss["Heading3"],
    fontName="Helvetica-Bold", fontSize=10.5, leading=14,
    textColor=INK, spaceBefore=10, spaceAfter=4, keepWithNext=True,
)
body = ParagraphStyle(
    "GpBody", parent=ss["BodyText"],
    fontName="Helvetica", fontSize=10.5, leading=15,
    textColor=INK_SOFT, spaceAfter=6,
)
bullet = ParagraphStyle(
    "GpBullet", parent=body,
    leftIndent=18, bulletIndent=4, spaceAfter=3,
)
toc_h1 = ParagraphStyle("TocH1", fontName="Helvetica-Bold", fontSize=11, leading=16, textColor=INK)

def callout(text):
    tbl = Table([[Paragraph(text, body)]], colWidths=[15 * cm])
    tbl.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, -1), CREAM),
        ("LEFTPADDING", (0, 0), (-1, -1), 12),
        ("RIGHTPADDING", (0, 0), (-1, -1), 12),
        ("TOPPADDING", (0, 0), (-1, -1), 10),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
        ("LINEBEFORE", (0, 0), (0, -1), 3, GOLD),
        ("ROUNDEDCORNERS", [6, 6, 6, 6]),
    ]))
    return tbl

def bullets(items):
    return [Paragraph(f"&bull;&nbsp;&nbsp;{i}", bullet) for i in items]

def on_page(canvas, doc):
    canvas.saveState()
    canvas.setFont("Helvetica", 8.5)
    canvas.setFillColor(MUTED)
    canvas.drawString(2 * cm, 1.2 * cm, "Golden Pangolin — Admin Manual (Mobile)")
    canvas.drawRightString(19 * cm, 1.2 * cm, f"Page {doc.page}")
    if doc.page > 1:
        canvas.setStrokeColor(GOLD)
        canvas.setLineWidth(1.2)
        canvas.line(2 * cm, 27.7 * cm, 19 * cm, 27.7 * cm)
    canvas.restoreState()

def on_cover(canvas, doc):
    canvas.saveState()
    canvas.setFillColor(GOLD)
    canvas.rect(0, 27.5 * cm, 21 * cm, 0.4 * cm, fill=1, stroke=0)
    canvas.setFont("Helvetica", 8.5)
    canvas.setFillColor(MUTED)
    canvas.drawCentredString(10.5 * cm, 1.2 * cm, "Golden Pangolin Tours & Safaris")
    canvas.restoreState()

# ---- Content -----
story = []

# Cover
story.append(Spacer(1, 6 * cm))
story.append(Paragraph("Golden Pangolin", title_style))
story.append(Paragraph("Admin Manual — On Your Phone", subtitle_style))
story.append(Spacer(1, 1 * cm))
story.append(Paragraph(
    "A pocket-sized guide to managing Golden Pangolin from your phone — "
    "logging in, finding the menu, working with the simplified mobile views, "
    "and getting everything done with one thumb.",
    ParagraphStyle("CoverIntro", parent=body, alignment=1, fontSize=11, leading=17, textColor=INK_SOFT)
))
story.append(Spacer(1, 1.5 * cm))
meta = Table(
    [
        ["Version",       "1.0"],
        ["Audience",      "Admins using a phone or small tablet"],
        ["Companion to",  "Golden Pangolin Admin Manual (desktop)"],
        ["Last updated",  "May 2026"],
    ],
    colWidths=[4 * cm, 11 * cm],
)
meta.setStyle(TableStyle([
    ("FONTNAME", (0, 0), (-1, -1), "Helvetica"),
    ("FONTSIZE", (0, 0), (-1, -1), 10),
    ("TEXTCOLOR", (0, 0), (0, -1), GOLD_DARK),
    ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
    ("TEXTCOLOR", (1, 0), (1, -1), INK),
    ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
    ("TOPPADDING", (0, 0), (-1, -1), 8),
    ("LINEBELOW", (0, 0), (-1, -2), 0.4, BORDER),
    ("LEFTPADDING", (0, 0), (-1, -1), 14),
    ("RIGHTPADDING", (0, 0), (-1, -1), 14),
]))
story.append(meta)
story.append(PageBreak())

# TOC
story.append(Paragraph("What's inside", h1))
toc = [
    "1. Before you start",
    "2. Logging in on your phone",
    "3. The hamburger menu (your main navigation)",
    "4. Reading the lists",
    "5. Adding photos",
    "6. Forms that look long — how to fill them quickly",
    "7. Drag-and-drop reordering on a touchscreen",
    "8. Flash messages and confirmations",
    "9. Common mobile tasks, step by step",
    "10. Tips for working faster",
    "11. Troubleshooting",
]
for t in toc:
    story.append(Paragraph(t, toc_h1))
story.append(PageBreak())

# 1. Before you start
story.append(Paragraph("1. Before you start", h1))
story.append(Paragraph(
    "Everything in the admin works on a phone — you don't need a laptop. The site "
    "automatically reshuffles itself when it sees a small screen, hiding things you "
    "don't need and giving you bigger taps for the things you do.",
    body))
story.append(Paragraph("You'll have the best experience if:", body))
story.extend(bullets([
    "Your phone is on a recent version of <b>Chrome</b>, <b>Safari</b>, or <b>Edge</b>.",
    "You're on a reasonable internet connection (the admin works on slow networks, but uploads need a few seconds).",
    "Your phone's screen rotation is on so you can briefly turn landscape to see more on lists if needed.",
]))
story.append(callout(
    "<b>Tip:</b> Save the admin URL to your phone's home screen. In Safari and Chrome, "
    "use the Share menu &rarr; <b>Add to Home Screen</b>. It launches like an app."
))

# 2. Logging in
story.append(Paragraph("2. Logging in on your phone", h1))
story.extend(bullets([
    "Open your phone's browser and go to <b>goldenpangolin.com/login</b>.",
    "Type your email and password.",
    "Tap <b>Log in</b>.",
    "If a one-time code is sent to your email, switch to your email app, copy the code, come back, and paste it.",
]))
story.append(Paragraph(
    "After a successful login you'll land on the dashboard with the welcome banner "
    "and stat tiles. The full admin lives behind the hamburger menu in the top-right.",
    body))

# 3. Hamburger menu
story.append(Paragraph("3. The hamburger menu (your main navigation)", h1))
story.append(Paragraph(
    "On a phone, the sidebar from the desktop view is gone — replaced by a tidy hamburger "
    "menu. Look for the three short horizontal lines (&equiv;) in the top-right corner.",
    body))
story.append(Paragraph("Tap it and you'll see:", body))
story.extend(bullets([
    "<b>Main</b> &mdash; Dashboard, Users (admin only), Settings (admin only).",
    "<b>Content</b> &mdash; Destinations, Activities, Packages, Accommodations, Accommodation Types, Categories, Blog Posts.",
    "<b>Trash</b> &mdash; restore or permanently delete things.",
    "<b>Account</b> &mdash; View Site, My Profile, Logout.",
]))
story.append(Paragraph("Three things to know:", h2))
story.extend(bullets([
    "<b>Tap any item</b> to navigate.",
    "<b>Tap outside the menu</b> to close it without going anywhere.",
    "<b>The page you're on</b> is highlighted in soft gold so you always know where you are.",
]))
story.append(callout(
    "<b>If the hamburger disappears</b> &mdash; you're probably on a desktop or tablet "
    "wide enough to switch back to the sidebar layout. Rotate to portrait or shrink the "
    "window and the hamburger comes back."
))

# 4. Lists
story.append(Paragraph("4. Reading the lists", h1))
story.append(Paragraph(
    "Lists (Destinations, Packages, Activities, Accommodation, Blog Posts, etc.) are "
    "long on desktop. On your phone, the admin <b>hides everything except the essentials</b> "
    "so you can tap without scrolling sideways:",
    body))
story.extend(bullets([
    "<b>Grip handle</b> on the left &mdash; for drag-and-drop reordering.",
    "<b>Name / Title</b> &mdash; the main label, with a short description underneath.",
    "<b>Actions</b> on the right &mdash; the edit (&#9999;) and delete (&#128465;) buttons.",
]))
story.append(Paragraph(
    "All the other columns (image thumbnail, country, price, type, active/featured "
    "status, etc.) are <b>still saved in the database</b>; they're just not shown on a phone "
    "to keep the list readable. To see them, view the same list on a desktop or rotate "
    "your phone to landscape on a larger device.",
    body))
story.append(Paragraph(
    "<b>To edit an item:</b> tap the pencil icon on its row.",
    body))
story.append(Paragraph(
    "<b>To delete:</b> tap the trash icon. You'll be asked to confirm. Deleted items "
    "go to Trash, not gone forever.",
    body))

# 5. Adding photos
story.append(Paragraph("5. Adding photos", h1))
story.append(Paragraph(
    "When you're adding an activity, package, accommodation, or blog post, look for the "
    "<b>photo picker</b> &mdash; a cream box with an upload-cloud icon that says <i>\"Tap to add "
    "photos\"</i>.",
    body))
story.append(Paragraph("On a phone:", h2))
story.extend(bullets([
    "<b>Tap the box.</b> Your phone offers <b>Photo Library</b>, <b>Take Photo</b>, or <b>Choose Files</b>.",
    "<b>Pick one or several photos.</b> They appear as thumbnails right below the box.",
    "<b>The first photo becomes the cover</b> &mdash; you'll see a gold <b>COVER</b> badge on it.",
    "<b>Don't like one?</b> Tap the small <b>&times;</b> on its corner to remove it (the rest stay).",
    "<b>Want more?</b> Tap the box again. The new ones get added to the existing selection &mdash; they don't replace.",
    "<b>Tap Save</b> when you're done. The site automatically shrinks each photo so it loads fast for visitors.",
]))
story.append(callout(
    "<b>Phone photos are perfect.</b> A regular phone photo (2-5 MB) is well within the "
    "limits. The site auto-compresses everything down to ~300 KB once uploaded &mdash; you "
    "don't need to resize manually."
))

# 6. Long forms
story.append(Paragraph("6. Forms that look long — how to fill them quickly", h1))
story.append(Paragraph(
    "Some forms (especially Activities and Packages) have many fields. On a phone they're "
    "stacked vertically, which makes them look long &mdash; but each field is full width and "
    "easy to tap.",
    body))
story.append(Paragraph("A few field types to know:", h2))

story.append(Paragraph("Pricing modes", h3))
story.append(Paragraph(
    "Tap one of the four cards: <b>Fixed</b>, <b>Free</b>, <b>Tiered</b>, or <b>From&hellip;</b>. "
    "The fields below the cards automatically change to match your choice &mdash; you only fill in "
    "what's relevant. If you change your mind, tap a different card.",
    body))

story.append(Paragraph("Tier availability (Packages)", h3))
story.append(Paragraph(
    "Three checkboxes: Local, Regional, International. All three start ticked. Untick a "
    "group to hide the package from those visitors. If the hero package isn't offered to "
    "the current visitor's tier, the site automatically picks a different one for them.",
    body))

story.append(Paragraph("Park fee toggle", h3))
story.append(Paragraph(
    "Just leave the box unticked if there's no extra fee. Tick it to reveal a dollar input "
    "and enter the amount &mdash; e.g. Victoria Falls park entry, conservation levy, boat launch.",
    body))

story.append(Paragraph("Multi-select rows (destinations / activities for a Package)", h3))
story.append(Paragraph(
    "Tap <b>+ Add Destination</b> or <b>+ Add Activity</b> to add a row. Each row is its own "
    "dropdown. Tap <b>Remove</b> on any row to take it out.",
    body))

# 7. Reordering
story.append(Paragraph("7. Drag-and-drop reordering on a touchscreen", h1))
story.append(Paragraph(
    "Most lists support drag-and-drop reordering. On a phone:",
    body))
story.extend(bullets([
    "Tap and <b>hold</b> the grip icon on the left of any row (the small dots-grid).",
    "<b>Drag</b> the row up or down. Other rows shift to make room.",
    "<b>Lift your finger</b> to drop. The new order saves automatically &mdash; no Save button needed.",
]))
story.append(callout(
    "<b>Phones can be fiddly with drag &amp; drop.</b> If you're struggling, the same lists "
    "are reorderable on a desktop where it's easier. The order you set anywhere applies "
    "to the public site immediately."
))

# 8. Flash messages
story.append(Paragraph("8. Flash messages and confirmations", h1))
story.append(Paragraph(
    "When you save something, a small white pill slides in from the top of the screen for "
    "a few seconds:",
    body))
story.extend(bullets([
    "<b>Green check</b> &mdash; the save worked.",
    "<b>Red cross</b> &mdash; something went wrong; the message tells you what.",
]))
story.append(Paragraph(
    "These auto-dismiss after about 3.5 seconds. You can also tap the small <b>&times;</b> on "
    "them to close them yourself.",
    body))

# 9. Common mobile tasks
story.append(Paragraph("9. Common mobile tasks, step by step", h1))

story.append(Paragraph("Add a new destination", h2))
story.extend(bullets([
    "Tap the hamburger &rarr; <b>Destinations</b>.",
    "Tap <b>+ Add New Destination</b> at the top.",
    "Fill in <b>Title</b>, <b>Country</b>, <b>Description</b>.",
    "Tap the photo picker box and pick a main image from your phone.",
    "Tap <b>Create</b>.",
]))

story.append(Paragraph("Add a new activity (with locals-free pricing)", h2))
story.extend(bullets([
    "Hamburger &rarr; <b>Activities</b> &rarr; <b>+ Add new</b>.",
    "Fill in the basics &mdash; name, destination, duration.",
    "Tap the <b>Tiered</b> pricing card.",
    "Type <b>0</b> in Local, type the price in Regional and International.",
    "Tick the park fee checkbox only if there's one, and enter the amount.",
    "Add up to 3 photos with the picker.",
    "Tap <b>Create Activity</b>.",
]))

story.append(Paragraph("Move a package to the homepage hero", h2))
story.extend(bullets([
    "Hamburger &rarr; <b>Packages</b>.",
    "Tap the pencil on the package you want to feature.",
    "Tick <b>Show this package on the homepage hero</b>.",
    "Tap <b>Update Package</b>. Any previous hero is automatically un-set.",
]))

story.append(Paragraph("Delete and recover a blog post", h2))
story.extend(bullets([
    "Hamburger &rarr; <b>Blog Posts</b>.",
    "Tap the trash icon on the post. Confirm.",
    "Changed your mind? Hamburger &rarr; <b>Trash</b>.",
    "Find the post and tap <b>Restore</b>.",
]))

# 10. Tips
story.append(Paragraph("10. Tips for working faster", h1))
story.extend(bullets([
    "<b>Add the admin to your home screen</b> for instant access (Share menu &rarr; Add to Home Screen).",
    "<b>Pull down to refresh</b> any list page if you've just changed something elsewhere and want fresh data.",
    "<b>Use voice-to-text</b> in your phone keyboard to dictate long descriptions and blog content.",
    "<b>Edit one thing at a time</b> on a phone &mdash; it's quicker than trying to do big batch changes on a small screen. For lots of edits at once, switch to a desktop.",
    "<b>Hide things instead of deleting</b> when you only need to take them down temporarily. Untick \"Visible on public site\" and Save.",
]))

# 11. Troubleshooting
story.append(Paragraph("11. Troubleshooting", h1))

story.append(Paragraph("Page looks broken or unstyled", h3))
story.append(Paragraph(
    "Force-refresh: pull down on the page, or close and reopen the browser tab. If a website "
    "update was just deployed, your phone may be showing a cached older version.",
    body))

story.append(Paragraph("Photo won't upload", h3))
story.append(Paragraph(
    "Check the size &mdash; if your phone takes very high-resolution photos (over 5 MB each), drop the "
    "quality in your camera app, or use the phone's built-in compression. Most phone photos are fine.",
    body))

story.append(Paragraph("Hamburger menu won't open", h3))
story.append(Paragraph(
    "Refresh the page and try again. If the icon isn't responding, you may have an old cached "
    "version of the page &mdash; clear your browser's cache for the site.",
    body))

story.append(Paragraph("I can't find a button or option", h3))
story.append(Paragraph(
    "On a phone the admin hides some columns and groups some buttons. If a feature still seems missing, "
    "log in on a laptop and see if it's there &mdash; some less-used controls only appear on wide screens.",
    body))

story.append(Paragraph("Something else is wrong", h3))
story.append(Paragraph(
    "Take a screenshot (Power + Volume Down on most phones) and send it to the developer with a "
    "one-line note of what you were trying to do. That gets things fixed fastest.",
    body))

story.append(Spacer(1, 1 * cm))
story.append(Paragraph(
    "<i>You're all set. The whole admin really does fit in your pocket.</i>",
    ParagraphStyle("Closing", parent=body, alignment=1, fontSize=10, textColor=MUTED)
))

# ---- Build -----
doc = SimpleDocTemplate(
    str(OUT),
    pagesize=A4,
    leftMargin=2 * cm,
    rightMargin=2 * cm,
    topMargin=2 * cm,
    bottomMargin=2 * cm,
    title="Golden Pangolin — Admin Manual (Mobile)",
    author="Golden Pangolin",
)
doc.build(story, onFirstPage=on_cover, onLaterPages=on_page)
print(f"Built: {OUT}")
