const GlowDivider = () => {
  return (
    <div className="w-full bg-[#081120] flex justify-center items-center py-4">
      <div className="relative h-[3px] w-48">
        <div className="absolute inset-0 rounded-full bg-[#FF6A00]" />
        <div className="absolute inset-0 rounded-full bg-[#FF6A00] blur-md opacity-80" />
      </div>
    </div>
  );
};

export default GlowDivider;