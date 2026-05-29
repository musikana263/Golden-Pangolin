"""
Builds the Golden Pangolin admin manual as a styled PDF.
Run: python scripts/build_admin_manual.py
Output: docs/Golden_Pangolin_Admin_Manual.pdf
"""
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    Image,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)
from reportlab.platypus.tableofcontents import TableOfContents

# ---- Brand colours ---------------------------------------------------------
GOLD      = colors.HexColor("#d4a03c")
GOLD_DARK = colors.HexColor("#b8872e")
INK       = colors.HexColor("#1a1208")
INK_SOFT  = colors.HexColor("#3a3a4a")
MUTED     = colors.HexColor("#6b7280")
CREAM     = colors.HexColor("#fdf5e6")
BORDER    = colors.HexColor("#e5e7eb")

ROOT = Path(__file__).resolve().parent.parent
OUT  = ROOT / "docs" / "Golden_Pangolin_Admin_Manual.pdf"
OUT.parent.mkdir(parents=True, exist_ok=True)

# ---- Styles ---------------------------------------------------------------
ss = getSampleStyleSheet()

title_style = ParagraphStyle(
    "GpTitle", parent=ss["Title"],
    fontName="Helvetica-Bold", fontSize=28, leading=34,
    textColor=INK, spaceAfter=10, alignment=1,
)
subtitle_style = ParagraphStyle(
    "GpSubtitle", parent=ss["Normal"],
    fontName="Helvetica", fontSize=13, leading=18,
    textColor=GOLD_DARK, spaceAfter=24, alignment=1,
)
h1 = ParagraphStyle(
    "GpH1", parent=ss["Heading1"],
    fontName="Helvetica-Bold", fontSize=20, leading=26,
    textColor=INK, spaceBefore=18, spaceAfter=10, keepWithNext=True,
)
h2 = ParagraphStyle(
    "GpH2", parent=ss["Heading2"],
    fontName="Helvetica-Bold", fontSize=14, leading=20,
    textColor=GOLD_DARK, spaceBefore=14, spaceAfter=6, keepWithNext=True,
)
h3 = ParagraphStyle(
    "GpH3", parent=ss["Heading3"],
    fontName="Helvetica-Bold", fontSize=11, leading=15,
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
small = ParagraphStyle(
    "GpSmall", parent=body,
    fontSize=9, leading=12, textColor=MUTED,
)
toc_h1 = ParagraphStyle("TocH1", fontName="Helvetica-Bold", fontSize=11, leading=16, textColor=INK)
toc_h2 = ParagraphStyle("TocH2", fontName="Helvetica", fontSize=10, leading=14, textColor=INK_SOFT, leftIndent=14)

# ---- Helpers ---------------------------------------------------------------
def heading(text, style):
    p = Paragraph(text, style)
    p._tocEntry = text  # used by AfterFlowable for the TOC (not strictly needed here)
    return p

def callout(text):
    """Yellow note box for important info."""
    tbl = Table(
        [[Paragraph(text, body)]],
        colWidths=[15 * cm],
    )
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

# ---- Page chrome -----------------------------------------------------------
def on_page(canvas, doc):
    canvas.saveState()
    # Footer
    canvas.setFont("Helvetica", 8.5)
    canvas.setFillColor(MUTED)
    canvas.drawString(2 * cm, 1.2 * cm, "Golden Pangolin — Admin Manual")
    canvas.drawRightString(19 * cm, 1.2 * cm, f"Page {doc.page}")
    # Top gold rule on inner pages
    if doc.page > 1:
        canvas.setStrokeColor(GOLD)
        canvas.setLineWidth(1.2)
        canvas.line(2 * cm, 27.7 * cm, 19 * cm, 27.7 * cm)
    canvas.restoreState()

def on_cover(canvas, doc):
    canvas.saveState()
    # Gold accent bar at top
    canvas.setFillColor(GOLD)
    canvas.rect(0, 27.5 * cm, 21 * cm, 0.4 * cm, fill=1, stroke=0)
    # Footer mark
    canvas.setFont("Helvetica", 8.5)
    canvas.setFillColor(MUTED)
    canvas.drawCentredString(10.5 * cm, 1.2 * cm, "Golden Pangolin Tours & Safaris")
    canvas.restoreState()

# ---- Build content --------------------------------------------------------
story = []

# Cover
story.append(Spacer(1, 6 * cm))
story.append(Paragraph("Golden Pangolin", title_style))
story.append(Paragraph("Admin User Manual", subtitle_style))
story.append(Spacer(1, 1 * cm))
story.append(Paragraph(
    "A plain-English guide to managing the Golden Pangolin website. "
    "Everything you need to know to add destinations, build packages, "
    "publish blog posts, and keep the site looking sharp.",
    ParagraphStyle("CoverIntro", parent=body, alignment=1, fontSize=11, leading=17, textColor=INK_SOFT)
))
story.append(Spacer(1, 1.5 * cm))
# Cover meta box
meta = Table(
    [
        ["Version", "1.0"],
        ["Audience", "Site administrators"],
        ["Last updated", "May 2026"],
    ],
    colWidths=[4 * cm, 8 * cm],
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

# Table of contents
story.append(Paragraph("Contents", h1))
toc_data = [
    "1. Logging in",
    "2. The dashboard at a glance",
    "3. Destinations",
    "4. Categories",
    "5. Activities",
    "6. Accommodation",
    "7. Packages",
    "8. Blog posts",
    "9. Reordering with drag and drop",
    "10. The trash bin",
    "11. Users",
    "12. Site settings",
    "13. Photo tips",
    "14. Common questions",
    "15. Getting help",
]
for t in toc_data:
    story.append(Paragraph(t, toc_h1))
story.append(PageBreak())

# 1. Logging in
story.append(Paragraph("1. Logging in", h1))
story.append(Paragraph(
    "Go to <b>goldenpangolin.com/login</b> and enter your email and password. "
    "Only verified admin accounts can reach the admin pages.",
    body))
story.append(Paragraph(
    "If you've forgotten your password, click <b>Forgot your password?</b> on the login screen. "
    "You'll receive a reset link by email.",
    body))
story.append(Paragraph(
    "Depending on your security settings, after entering your password you may be asked for a "
    "one-time code sent to your email. Type the code in the next screen to finish logging in.",
    body))
story.append(Spacer(1, 0.5 * cm))
story.append(callout(
    "<b>Tip:</b> If you don't receive the password reset email within 2 minutes, check your "
    "junk/spam folder. Reset links expire after 60 minutes."
))

# 2. Dashboard
story.append(Paragraph("2. The dashboard at a glance", h1))
story.append(Paragraph(
    "The dashboard is the first page you see after logging in. It gives you a quick overview of the site:",
    body))
story.extend(bullets([
    "<b>Welcome banner</b> with today's date.",
    "<b>Six stat tiles</b> across the top — number of destinations, categories, packages, stays, blog posts, and users.",
    "<b>Quick action buttons</b> — one-click shortcuts to add a destination, write a blog post, create a category, or add accommodation.",
    "<b>Recent destinations</b> and <b>Recent blog posts</b> — see what was added or changed lately.",
    "<b>Recent activity timeline</b> — a live feed of changes across the site.",
]))
story.append(Paragraph(
    "Use the left-hand menu (or the top menu on mobile) to jump to any management section.",
    body))

# 3. Destinations
story.append(Paragraph("3. Destinations", h1))
story.append(Paragraph(
    "A <b>destination</b> is a place — Victoria Falls, Hwange, Lake Kariba, etc. Each destination "
    "has its own page on the public site.",
    body))

story.append(Paragraph("Adding a destination", h2))
story.append(Paragraph("Click <b>Add New Destination</b> from the dashboard or the destinations menu, then fill in:", body))
story.extend(bullets([
    "<b>Title</b> — e.g. \"Victoria Falls\".",
    "<b>Country</b> — defaults to Zimbabwe.",
    "<b>Description</b> — what visitors will read on the destination page.",
    "<b>Image</b> — the main photo (gets resized automatically).",
    "<b>Publish date</b> — when the destination becomes visible. Usually leave it as today.",
]))
story.append(Paragraph("Click <b>Create</b> to save.", body))

story.append(Paragraph("Editing or deleting", h2))
story.append(Paragraph(
    "On the destinations list, each row has a pen icon for editing and a trash icon for deleting. "
    "Deleted destinations don't vanish immediately — they go to the <b>Trash bin</b> and can be restored from there.",
    body))

story.append(Paragraph("Reordering", h2))
story.append(Paragraph(
    "You can drag and drop the rows on the destinations list to change the order they appear on the public site. "
    "Click the small grip icon on the left of any row and drag it up or down.",
    body))

# 4. Categories
story.append(Paragraph("4. Categories", h1))
story.append(Paragraph(
    "<b>Categories</b> are themes used to group blog posts and activities (Safari, Cultural, Adventure, "
    "Wildlife, etc.). You need at least one category before you can write blog posts.",
    body))
story.append(Paragraph("Adding a category", h2))
story.extend(bullets([
    "From the menu, click <b>Categories &rarr; Add new</b>.",
    "Enter a <b>name</b> and an optional <b>description</b>.",
    "Save.",
]))
story.append(Paragraph(
    "Categories can be edited or deleted at any time from the categories list.",
    body))

# 5. Activities
story.append(Paragraph("5. Activities", h1))
story.append(Paragraph(
    "<b>Activities</b> are individual things to do at a destination — boat cruises, game drives, "
    "helicopter flights, cultural visits, etc.",
    body))

story.append(Paragraph("Adding an activity", h2))
story.append(Paragraph("Click <b>Activities &rarr; Add new</b>, then fill in:", body))
story.extend(bullets([
    "<b>Activity name</b>.",
    "<b>Destination</b> — pick which destination this activity belongs to.",
    "<b>Category</b> — optional.",
    "<b>Duration</b> — e.g. \"2 hours\", \"Half day\", \"Full day\".",
]))

story.append(Paragraph("Pricing mode (important)", h3))
story.append(Paragraph(
    "Pick <b>one</b> of four ways to price the activity:",
    body))
story.extend(bullets([
    "<b>Fixed</b> — one price for everyone. Enter the dollar amount.",
    "<b>Free</b> — no charge for anyone. Nothing else to enter.",
    "<b>Tiered</b> — different price per traveller type. Three boxes for Local, Regional (African), and International. "
    "Leave a box blank to <b>hide</b> the activity from that group. Type <b>0</b> to mark it <b>free</b> for that group.",
    "<b>From...</b> — a free-text label like \"From $50 per person\" for activities where the price varies.",
]))
story.append(callout(
    "<b>How tiered pricing helps:</b> If you offer a local-resident discount, set "
    "<b>Local = 25</b>, <b>Regional = 40</b>, <b>International = 60</b>. Each visitor "
    "automatically sees the right price based on the traveller type they picked when they "
    "first visited the site."
))

story.append(Paragraph("Park / conservation fee", h3))
story.append(Paragraph(
    "If the activity includes an extra fee (Victoria Falls park entry, a boat-launch levy, etc.), "
    "tick the <b>This activity has a park / conservation fee</b> box. A dollar input appears — "
    "enter the fee amount. It will show on the public page as <b>Park fee: $X</b>.",
    body))
story.append(Paragraph(
    "Leave the box unticked if there's no extra fee.",
    body))

story.append(Paragraph("Photos and visibility", h3))
story.extend(bullets([
    "Add up to <b>3 photos</b>. The first becomes the cover; the rest cycle in a hover gallery.",
    "Write a one- or two-sentence <b>short description</b> shown under the activity on the destination page.",
    "Leave <b>Visible on public site</b> ticked. Untick it to temporarily hide the activity without deleting it.",
]))
story.append(Paragraph("Click <b>Create Activity</b>.", body))

story.append(Paragraph("Reordering", h2))
story.append(Paragraph(
    "Drag and drop rows on the activities list to set their display order on the destination page.",
    body))

# 6. Accommodation
story.append(Paragraph("6. Accommodation", h1))
story.append(Paragraph(
    "<b>Accommodation</b> entries are hotels, lodges, tented camps, and similar properties.",
    body))

story.append(Paragraph("Adding accommodation", h2))
story.append(Paragraph("Click <b>Accommodation &rarr; Add new</b>, then fill in:", body))
story.extend(bullets([
    "<b>Name</b>.",
    "<b>Type</b> — pick from the list (Lodge, Hotel, Tented Camp, etc.).",
    "<b>Location</b> — e.g. \"Victoria Falls\".",
    "<b>Country</b>.",
    "<b>Description</b>.",
    "<b>Price From</b> — a free-text price label (e.g. \"From $250 per night\").",
    "<b>Amenities</b> — tick the relevant boxes (WiFi, pool, restaurant, etc.).",
    "<b>Photos</b> — up to 6 photos.",
    "<b>Visible on public site</b>.",
]))

story.append(Paragraph("Accommodation types", h2))
story.append(Paragraph(
    "The list of accommodation <b>types</b> (Lodge, Hotel, Tented Camp, Guesthouse, etc.) is "
    "managed under <b>Accommodation Types</b> in the menu. Add, edit, or delete types as needed. "
    "Each type can have its own icon and short description.",
    body))

story.append(Paragraph("Reordering", h2))
story.append(Paragraph(
    "Drag and drop rows on the accommodation list. The order you set there applies everywhere on the public site, "
    "including the \"Where to stay near...\" section on each destination's page.",
    body))

# 7. Packages
story.append(Paragraph("7. Packages", h1))
story.append(Paragraph(
    "<b>Packages</b> are multi-day tour bundles — itineraries that combine destinations, activities, "
    "and (optionally) an accommodation.",
    body))

story.append(Paragraph("Adding a package", h2))
story.append(Paragraph("Click <b>Packages &rarr; Add new</b>, then fill in:", body))
story.extend(bullets([
    "<b>Name</b>.",
    "<b>Destinations</b> — add every destination this package covers. Use <b>+ Add destination</b> to add more rows.",
    "<b>Activities</b> — add each activity included in the package.",
    "<b>Short description</b>.",
    "<b>Duration</b> — e.g. \"7 Days / 6 Nights\".",
    "<b>Group size</b> — e.g. \"2-10 People\".",
    "<b>Price</b> — a free-text price (e.g. \"From USD 1,890 per person\").",
    "<b>Accommodation</b> — optional, link to one of your accommodations.",
    "<b>Image</b> — the package's main photo.",
]))

story.append(Paragraph("Visibility & feature toggles", h3))
story.extend(bullets([
    "<b>Visible on public site</b> — leave ticked to show the package.",
    "<b>Featured package</b> — ticks the package onto the homepage \"Curated Packages\" section.",
    "<b>Show this package on the homepage hero</b> — places this package in the big banner at the top of the homepage. <b>Only one package at a time</b> can hold this spot — ticking it automatically removes any other package from the hero slot.",
]))

story.append(Paragraph("Available to (traveller tier)", h3))
story.append(Paragraph(
    "Three checkboxes for which traveller types can book this package:",
    body))
story.extend(bullets([
    "<b>Local travellers</b>",
    "<b>Regional (African) travellers</b>",
    "<b>International travellers</b>",
]))
story.append(Paragraph(
    "By default all three are ticked, meaning the package is offered to everyone. Untick any group "
    "to hide the package from those visitors.",
    body))
story.append(callout(
    "<b>Smart fallback:</b> If the package you chose for the homepage hero is hidden from a "
    "visitor's traveller tier, the site automatically picks a different package for that "
    "visitor — so nobody sees an advert for a tour they can't actually book."
))

story.append(Paragraph("Reordering", h2))
story.append(Paragraph(
    "Drag and drop rows on the packages list to control the order on the public site.",
    body))

# 8. Blog
story.append(Paragraph("8. Blog posts", h1))
story.append(Paragraph("Adding a blog post", h2))
story.append(Paragraph("Click <b>Blog &rarr; Add new</b> (or use the \"Write Blog Post\" shortcut on the dashboard), then fill in:", body))
story.extend(bullets([
    "<b>Title</b>.",
    "<b>Category</b>.",
    "<b>Short description</b> — the teaser shown in lists.",
    "<b>Content</b> — the full article.",
    "<b>Image</b> — the cover photo.",
    "<b>Publish date</b> — when it becomes public.",
]))
story.append(Paragraph(
    "Posts can be dragged and dropped to reorder. New posts default to the top of the list.",
    body))

# 9. Reorder
story.append(Paragraph("9. Reordering with drag and drop", h1))
story.append(Paragraph(
    "Most lists in the admin (destinations, activities, accommodation, packages, blog posts) "
    "support drag-and-drop reordering:",
    body))
story.extend(bullets([
    "Hover over any row — you'll see a small <b>grip handle</b> on the left.",
    "Click and hold the handle, then drag the row up or down.",
    "Release. The new order saves automatically.",
]))
story.append(Paragraph(
    "The order you set in the admin is the order visitors see on the public site.",
    body))

# 10. Trash
story.append(Paragraph("10. The trash bin", h1))
story.append(Paragraph(
    "When you delete a destination, package, accommodation, or blog post, it goes to the <b>Trash</b> "
    "first — it isn't permanently removed.",
    body))
story.append(Paragraph("Click <b>Trash</b> in the menu to see everything that's been deleted. From there you can:", body))
story.extend(bullets([
    "<b>Restore</b> — bring the item back as if it was never deleted.",
    "<b>Delete permanently</b> — wipe the item and its photos for good.",
]))
story.append(callout(
    "<b>Peace of mind:</b> Items in the trash are not visible on the public site, but they're "
    "still recoverable. Use this as a safety net rather than worrying about \"undo\"."
))

# 11. Users
story.append(Paragraph("11. Users", h1))
story.append(Paragraph(
    "The <b>Users</b> page lists everyone who has an account on the site. From here you can:",
    body))
story.extend(bullets([
    "See each user's name, email, and role (Admin or regular user).",
    "Edit a user's details.",
    "Promote a user to <b>Admin</b> so they can manage the site too.",
    "Demote an admin back to a regular user.",
    "Delete a user.",
]))
story.append(Paragraph(
    "You can also create brand-new admin accounts directly from this page.",
    body))

# 12. Settings
story.append(Paragraph("12. Site settings", h1))
story.append(Paragraph(
    "The <b>Settings</b> page lets you change site-wide options:",
    body))
story.extend(bullets([
    "<b>WhatsApp number</b> — used by the floating chat button and every \"Enquire\" link.",
    "<b>Email addresses</b> — where contact-form messages are sent.",
    "<b>Social media links</b> — Facebook, Instagram, TikTok.",
    "Other contact and branding details.",
]))
story.append(Paragraph(
    "Changes here apply across the entire site immediately after saving.",
    body))

# 13. Photo tips
story.append(Paragraph("13. Photo tips", h1))
story.append(Paragraph(
    "The site <b>automatically shrinks large photos when you upload them</b> — you can drop in a "
    "5 MB phone photo and the site will resize and compress it. You do not need to compress images yourself.",
    body))
story.append(Paragraph("A few tips for great-looking cards:", body))
story.extend(bullets([
    "Use <b>landscape</b> photos (wider than tall) where possible — they look better in card layouts.",
    "The <b>first photo</b> you add becomes the cover everywhere.",
    "For activities and accommodation, the other photos cycle in a hover/swipe gallery on the public page.",
    "Bright, high-contrast photos read best on small phone screens.",
]))

# 14. Common questions
story.append(Paragraph("14. Common questions", h1))

story.append(Paragraph("How do I hide something temporarily without deleting it?", h3))
story.append(Paragraph(
    "Edit the item and untick <b>Visible on public site</b>. Save. The item stays in your admin but "
    "disappears from the public site. Re-tick the box to bring it back.",
    body))

story.append(Paragraph("I deleted something by mistake — can I get it back?", h3))
story.append(Paragraph(
    "Yes. Go to <b>Trash</b> in the menu, find the item, and click <b>Restore</b>. It returns to its "
    "section exactly as it was.",
    body))

story.append(Paragraph("Why isn't a package showing on the homepage hero?", h3))
story.append(Paragraph(
    "Check the package edit screen: it needs <b>Visible on public site</b> ticked, <b>Show this "
    "package on the homepage hero</b> ticked, and the current visitor's traveller tier must be "
    "ticked under <b>Available to</b>. If the visitor's tier isn't included, the site shows a "
    "different package to them.",
    body))

story.append(Paragraph("A photo looks stretched or blurry", h3))
story.append(Paragraph(
    "Try uploading a higher-resolution version. The site shrinks large images down but cannot "
    "improve a small image. Aim for at least 1200 pixels wide.",
    body))

story.append(Paragraph("How do I change the order of activities on a destination page?", h3))
story.append(Paragraph(
    "Go to the <b>Activities</b> list, find your activity, and drag its row up or down using the grip handle. "
    "The new order shows on the public destination page immediately.",
    body))

story.append(Paragraph("How do I make an activity free for locals but priced for others?", h3))
story.append(Paragraph(
    "Edit the activity, choose <b>Tiered</b> as the pricing mode, set <b>Local = 0</b>, and enter "
    "the price you charge regional and international visitors in the other two boxes. Local "
    "visitors will see \"Free\"; everyone else sees their tier's price.",
    body))

# 15. Help
story.append(Paragraph("15. Getting help", h1))
story.append(Paragraph(
    "If something isn't working as expected, the fastest way to get help is:",
    body))
story.extend(bullets([
    "Take a <b>screenshot</b> of the screen showing the problem.",
    "Note down <b>what you were trying to do</b> when it happened.",
    "Send both to the developer.",
]))
story.append(Paragraph(
    "Most issues clear up within minutes once we can see what you see.",
    body))

story.append(Spacer(1, 1 * cm))
story.append(Paragraph(
    "<i>Thanks for taking such good care of Golden Pangolin.</i>",
    ParagraphStyle("Closing", parent=body, alignment=1, fontSize=10, textColor=MUTED)
))

# ---- Build -----------------------------------------------------------------
doc = SimpleDocTemplate(
    str(OUT),
    pagesize=A4,
    leftMargin=2 * cm,
    rightMargin=2 * cm,
    topMargin=2 * cm,
    bottomMargin=2 * cm,
    title="Golden Pangolin — Admin Manual",
    author="Golden Pangolin",
)

doc.build(story, onFirstPage=on_cover, onLaterPages=on_page)
print(f"Built: {OUT}")
