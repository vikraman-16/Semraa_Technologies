"use client";

const TalkButton = () => {
  const handleClick = () => {
    const section = document.getElementById("contact-form");

    if (section) {
      section.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  };

  return (
    <button
      type="button"
      onClick={handleClick}
      className="app__filled_btn min-w-[12rem]"
    >
      Get in Touch
    </button>
  );
};

export default TalkButton;