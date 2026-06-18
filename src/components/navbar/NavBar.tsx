"use client";

import { useRef, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBars, faXmark } from "@fortawesome/free-solid-svg-icons";
import useOnClickOutside from "@/hooks/useOnClickOutside";
import navMenus from "@/data/navMenus";
import MenuItems from "@/components/navbar/MenuItems";
import useVisibleSection from "@/hooks/useVisibleSection";
import useScrolled from "@/hooks/useScrolled";

const NavBar = () => {
  const mobileMenuRef = useRef<HTMLDivElement>(null);
  const navRef = useRef<HTMLDivElement>(null);

  const visibleSection = useVisibleSection();
  const isScrolled = useScrolled();

  const [mobileMenuVisible, setMobileMenuVisible] = useState(false);

  const showMobileMenu = () => {
    if (!mobileMenuRef.current) return;

    mobileMenuRef.current.classList.remove(
      "hidden",
      "opacity-0",
      "z-0",
      "invisible"
    );

    mobileMenuRef.current.classList.add(
      "flex",
      "opacity-100",
      "z-[1000]",
      "visible"
    );

    setMobileMenuVisible(true);
  };

  const hideMobileMenu = () => {
    if (!mobileMenuRef.current) return;

    mobileMenuRef.current.classList.remove(
      "flex",
      "opacity-100",
      "z-[1000]",
      "visible"
    );

    mobileMenuRef.current.classList.add(
      "hidden",
      "opacity-0",
      "z-0",
      "invisible"
    );

    setMobileMenuVisible(false);
  };

  const toggleMenu = () => {
    mobileMenuVisible ? hideMobileMenu() : showMobileMenu();
  };

  useOnClickOutside(navRef, hideMobileMenu);

  return (
    <div
      ref={navRef}
      className={`
        fixed left-0 top-0 z-[1000]
        h-[8rem]
        w-full
        border-b border-[#FF6A00]/25
        bg-[#081120]
        text-white
        ${
          isScrolled
            ? "shadow-[0_10px_30px_rgba(0,0,0,0.35)]"
            : "shadow-none"
        }
      `}
    >
      <div className="flex h-full w-full items-center justify-between px-4 md:px-8">
        <Link href="/" className="flex items-center justify-start">
          <Image
            src="/logo-horizontal-new.png"
            alt="Semraa Technology"
            width={2800}
            height={350}
            priority
            className="
              w-[360px]
              h-auto
              object-contain
              object-left
              select-none
              md:w-[400px]
              lg:w-[450px]
              xl:w-[450px]
            "
          />
        </Link>

        {/*
        <div className="ml-auto flex justify-end lg:relative">
          <button
            type="button"
            name="menu-btn"
            aria-label="menu button"
            className="
              flex h-14 w-14 items-center justify-center
              rounded-2xl
              bg-[#FF6A00]
              text-white
              hover:bg-[#FF8124]
              md:h-16 md:w-16
            "
            onClick={toggleMenu}
          >
            {mobileMenuVisible ? (
              <FontAwesomeIcon icon={faXmark} className="text-3xl" />
            ) : (
              <FontAwesomeIcon icon={faBars} className="text-3xl" />
            )}
          </button>

          <div
            ref={mobileMenuRef}
            className="
              hidden invisible opacity-0 z-0
              absolute right-0 top-[calc(100%+1rem)]
              h-auto max-h-[24rem]
              w-[16rem]
              flex-col items-center justify-start
              overflow-hidden
              rounded-2xl
              border border-[#FF6A00]/25
              bg-[#101A2B]
              p-4
              text-center text-white

              max-lg:fixed
              max-lg:left-0
              max-lg:right-0
              max-lg:top-0
              max-lg:h-screen
              max-lg:max-h-screen
              max-lg:w-screen
              max-lg:rounded-none
              max-lg:border-0
              max-lg:bg-[#081120]
              max-lg:p-6
              max-lg:pt-28
            "
          >
            <button
              type="button"
              name="close-mobile-menu-btn"
              aria-label="close-mobile-menu-btn"
              className="
                absolute right-6 top-6 z-[1001]
                flex h-14 w-14 items-center justify-center
                rounded-2xl
                bg-[#FF6A00]
                text-white
                hover:bg-[#FF8124]
                lg:hidden
              "
              onClick={toggleMenu}
            >
              <FontAwesomeIcon icon={faXmark} className="text-3xl" />
            </button>

            <div className="flex w-full flex-col items-center justify-center gap-3 list-none lg:justify-start">
              {navMenus.map((menu, index) => (
                <MenuItems
                  items={menu}
                  key={`mobile-menu-item-${index}`}
                  depthLevel={0}
                  mobileNav={mobileMenuVisible}
                  handleCloseMobileMenu={hideMobileMenu}
                  current={visibleSection}
                />
              ))}
            </div>
          </div>
        </div>
        */}
      </div>
    </div>
  );
};

export default NavBar;

