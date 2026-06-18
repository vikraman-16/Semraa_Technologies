"use client";

import { useEffect, useMemo, useState } from "react";
import navMenus from "@/data/navMenus";

type SectionItem = {
  id: string;
};

function useVisibleSection(sections: SectionItem[] = navMenus) {
  const sectionIds = useMemo(
    () => sections.map((section) => section.id).filter(Boolean),
    [sections]
  );

  const [visibleSectionId, setVisibleSectionId] = useState(
    sectionIds[0] || ""
  );

  useEffect(() => {
    if (!sectionIds.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        const visibleEntries = entries
          .filter((entry) => entry.isIntersecting)
          .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

        if (!visibleEntries.length) return;

        const id = visibleEntries[0].target.id;

        setVisibleSectionId((prev) => {
          if (prev === id) return prev;
          return id;
        });
      },
      {
        root: null,
        threshold: [0.25, 0.5, 0.75],
        rootMargin: "-20% 0px -55% 0px",
      }
    );

    sectionIds.forEach((id) => {
      const element = document.getElementById(id);
      if (element) observer.observe(element);
    });

    return () => {
      observer.disconnect();
    };
  }, [sectionIds]);

  return visibleSectionId;
}

export default useVisibleSection;