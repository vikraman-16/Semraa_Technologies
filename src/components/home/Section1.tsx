"use client";

import { useEffect, useState } from "react";
import Balancer from "react-wrap-balancer";
import ConstraintedBox from "@/components/core/ConstraintedBox";
import ResponsiveBox from "@/components/core/ResponsiveBox";
import GridBox from "@/components/core/GridBox";
import Column from "@/components/core/Column";
import TalkButton from "./components/TalkButton";

const codeLines = [
  `const idea = "Your Business";`,
  `const ui = "Premium Design";`,
  `const app = "Web + Mobile";`,
  `const api = "Secure Backend";`,
  `launch("Success");`,
];

const HomeSection1 = ({ id }: Readonly<{ id?: string }>) => {
  const [typedCode, setTypedCode] = useState("");

  useEffect(() => {
    const fullCode = codeLines.join("\n");
    let index = 0;
    let timeout: NodeJS.Timeout;

    const typeCode = () => {
      setTypedCode(fullCode.slice(0, index));
      index++;

      if (index <= fullCode.length) {
        timeout = setTimeout(typeCode, 45);
      } else {
        timeout = setTimeout(() => {
          index = 0;
          setTypedCode("");
          typeCode();
        }, 1800);
      }
    };

    typeCode();

    return () => clearTimeout(timeout);
  }, []);

  return (
    <ResponsiveBox
      id={id}
      classNames="
        relative overflow-hidden
        bg-[#081120]
        min-h-screen
        items-center justify-center
      "
    >
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_82%_22%,rgba(255,106,0,0.14),transparent_28%),radial-gradient(circle_at_8%_85%,rgba(255,106,0,0.08),transparent_24%)]" />

      <ConstraintedBox classNames="relative z-[5] px-6 pt-8 pb-10 lg:pt-10 lg:pb-12">
        <GridBox
          classNames="
            w-full
            !grid-cols-1
            lg:!grid-cols-[0.92fr_1.08fr]
            items-center
            !gap-10
            xl:!gap-12
          "
        >
          <Column classNames="w-full !justify-center items-center lg:items-start">
            <h1
              className="
                max-w-[680px]
                text-4xl/tight
                md:text-5xl/tight
                xl:text-6xl/tight
                font-extrabold
                text-white
                text-center
                lg:text-start
              "
            >
              <Balancer>
                Transforming Your Ideas into Innovative Solutions
              </Balancer>
            </h1>

            <p
              className="
                mt-6
                max-w-[620px]
                text-base/7
                md:text-lg/8
                text-[#E4ECF7]
                text-center
                lg:text-start
              "
            >
              <Balancer>
We Design Your Success.<br></br>
Crafting technology-driven solutions that help businesses innovate, grow and stay ahead in a digital world.
              </Balancer>
            </p>

            <Column classNames="gap-4 mt-8 lg:mt-10 lg:flex-row">
              <TalkButton />
            </Column>
          </Column>

          <div className="relative flex w-full items-center justify-center lg:justify-end">
            <div
              className="
                dashboard-card
                relative
                w-full
                max-w-[700px]
                overflow-hidden
                rounded-[1.75rem]
                border
                border-[#FF6A00]/30
                bg-[#0D1728]/95
                p-5
              "
            >
              <div className="pointer-events-none absolute -right-24 -top-24 h-56 w-56 rounded-full bg-[#FF6A00]/10" />
              <div className="pointer-events-none absolute -bottom-24 -left-24 h-60 w-60 rounded-full bg-[#FF6A00]/6" />

              <div className="relative mb-4 flex items-center justify-between border-b border-white/12 pb-4">
                <div className="flex gap-2">
                  <span className="h-3 w-3 rounded-full bg-[#FF6A00]" />
                  <span className="h-3 w-3 rounded-full bg-[#D4E0EF]" />
                  <span className="h-3 w-3 rounded-full bg-[#7A8AA0]" />
                </div>

                <p className="live-build-text text-xs md:text-sm font-bold tracking-[0.25em] text-[#FF8124]">
                  LIVE BUILD
                </p>
              </div>

              <div className="relative grid gap-4 md:grid-cols-[1.05fr_0.95fr]">
                <div className="code-window">
                  <p className="mb-4 text-sm font-bold text-[#FF8124]">
                    semraa.build()
                  </p>

                  <pre className="typing-code">
                    <code>
                      {typedCode}
                      <span className="typing-cursor">|</span>
                    </code>
                  </pre>
                </div>

                <div className="grid gap-4">
                  <div className="stat-card">
                    <span>01</span>
                    <h3>Web Platforms</h3>
                    <p>Fast, modern websites</p>
                  </div>

                  <div className="stat-card">
                    <span>02</span>
                    <h3>Mobile Apps</h3>
                    <p>Android & iOS solutions</p>
                  </div>

                  <div className="stat-card">
                    <span>03</span>
                    <h3>Backend</h3>
                    <p>Secure APIs & servers</p>
                  </div>
                </div>
              </div>

              <div className="relative mt-4 grid gap-4 sm:grid-cols-3">
                <div className="mini-card">
                  <span>Plan</span>
                </div>
                <div className="mini-card">
                  <span>Design</span>
                </div>
                <div className="mini-card">
                  <span>Launch</span>
                </div>
              </div>
            </div>
          </div>
        </GridBox>
      </ConstraintedBox>
    </ResponsiveBox>
  );
};

export default HomeSection1;