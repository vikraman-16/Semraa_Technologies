import { ISkillListItem, SkillLevel } from "@/types";

const skills: ISkillListItem[] = [
  {
    title: "Software Development",
    items: [
      { title: "Python", level: SkillLevel.Expert },
      { title: "C#", level: SkillLevel.Expert },
      { title: ".NET", level: SkillLevel.Intermediate },
      { title: "JavaScript", level: SkillLevel.Expert },
      { title: "TypeScript", level: SkillLevel.Intermediate },
      { title: "PHP", level: SkillLevel.Intermediate },
    ],
  },

  {
    title: "Frontend Technologies",
    items: [
      { title: "Angular", level: SkillLevel.Intermediate },
      { title: "React", level: SkillLevel.Expert },
      { title: "Next.js", level: SkillLevel.Expert },
      { title: "HTML", level: SkillLevel.Expert },
      { title: "CSS", level: SkillLevel.Expert },
    ],
  },

  {
    title: "Backend Technologies",
    items: [
      { title: "Node.js", level: SkillLevel.Expert },
      { title: "Express", level: SkillLevel.Expert },
      { title: "REST APIs", level: SkillLevel.Expert },
    ],
  },

  {
    title: "Cloud Technologies",
    items: [
      { title: "Azure", level: SkillLevel.Intermediate },
      { title: "AWS", level: SkillLevel.Intermediate },
      { title: "GCP", level: SkillLevel.Intermediate },
      { title: "Firebase", level: SkillLevel.Expert },
    ],
  },

  {
    title: "Database Technologies",
    items: [
      { title: "MongoDB", level: SkillLevel.Expert },
      { title: "MySQL", level: SkillLevel.Expert },
    ],
  },

  {
    title: "Game Development",
    items: [
      { title: "Unity", level: SkillLevel.Expert },
      { title: "Android Games", level: SkillLevel.Expert },
      { title: "PC Games", level: SkillLevel.Expert },
      { title: "WebGL Games", level: SkillLevel.Intermediate },
    ],
  },

  {
    title: "DevOps & Tools",
    items: [
      { title: "Git", level: SkillLevel.Expert },
      { title: "GitHub", level: SkillLevel.Expert },
      { title: "VS Code", level: SkillLevel.Expert },
      { title: "Ubuntu", level: SkillLevel.Intermediate },
    ],
  },

  {
    title: "Digital Marketing",
    items: [
      { title: "SEO", level: SkillLevel.Intermediate },
      { title: "Online Marketing", level: SkillLevel.Intermediate },
    ],
  },

  {
    title: "Creative Media",
    items: [
      { title: "Video Editing", level: SkillLevel.Expert },
      { title: "2D Animation", level: SkillLevel.Intermediate },
      { title: "3D Animation", level: SkillLevel.Intermediate },
      { title: "Ad Film Making", level: SkillLevel.Intermediate },
    ],
  },
];

export default skills;