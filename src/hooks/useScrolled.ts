"use client";

import { useEffect, useState } from "react";

const useScrolled = (offset: number = 40) => {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const validOffset =
      typeof offset === "number" && isFinite(offset) && offset > 0
        ? offset
        : 40;

    let ticking = false;

    const handleScroll = () => {
      if (ticking) return;

      ticking = true;

      window.requestAnimationFrame(() => {
        const nextValue = window.scrollY > validOffset;

        setScrolled((prev) => {
          if (prev === nextValue) return prev;
          return nextValue;
        });

        ticking = false;
      });
    };

    handleScroll();

    window.addEventListener("scroll", handleScroll, {
      passive: true,
    });

    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, [offset]);

  return scrolled;
};

export default useScrolled;